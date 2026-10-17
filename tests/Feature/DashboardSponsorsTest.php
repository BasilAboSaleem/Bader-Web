<?php

namespace Tests\Feature;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\User;
use App\Notifications\SponsorInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DashboardSponsorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sponsor_accounts_are_kept_out_of_the_team_dashboard(): void
    {
        $sponsorUser = Sponsor::factory()->withAccount()->create()->user;
        $admin = User::factory()->create();

        $this->actingAs($sponsorUser)->get(route('dashboard'))->assertRedirect(route('sponsor.home'));
        $this->actingAs($sponsorUser)->get(route('dashboard.programs.index'))->assertForbidden();
        $this->actingAs($sponsorUser)->get(route('dashboard.orphans.index'))->assertForbidden();
        $this->actingAs($sponsorUser)->get(route('dashboard.sponsors.index'))->assertForbidden();
        $this->actingAs($sponsorUser)->get(route('dashboard.settings.edit'))->assertForbidden();

        $this->actingAs($admin)->get(route('sponsor.home'))->assertForbidden();
    }

    public function test_a_sponsor_sees_only_their_own_sponsored_people_and_no_private_details(): void
    {
        $sponsor = Sponsor::factory()->withAccount()->create();
        $family = OrphanFamily::factory()->approved()->widowFamily()->create();
        $orphan = Orphan::factory()->for($family, 'family')->create([
            'full_name' => 'يوسف محمد النجار',
            'national_id' => '412345678',
            'birth_date' => now()->subYears(9)->toDateString(),
            'education_stage' => 'primary',
            'health_status' => 'chronic_illness',
            'health_notes' => 'يحتاج دواء الربو يومياً',
        ]);
        Sponsorship::factory()->forMother($family)->for($sponsor)->create(['monthly_amount' => 300, 'currency' => 'ILS']);
        Sponsorship::factory()->for($sponsor)->for($orphan)->create(['orphan_family_id' => $family->id, 'monthly_amount' => 150, 'currency' => 'ILS']);
        $otherSponsorship = Sponsorship::factory()->create();

        $this->actingAs($sponsor->user)->get(route('sponsor.home'))
            ->assertOk()
            ->assertSee('يوسف محمد النجار')
            ->assertSee(__('sponsorship.age_years', ['age' => 9]))
            ->assertSee(__('orphans.education.primary'))
            ->assertSee(__('orphans.health.chronic_illness'))
            ->assertSee($family->mother_full_name)
            ->assertSee('₪450.00')
            ->assertDontSee($otherSponsorship->orphan->full_name)
            ->assertDontSee('412345678')
            ->assertDontSee('يحتاج دواء الربو')
            ->assertDontSee($family->mother_national_id)
            ->assertDontSee($family->mother_phone)
            ->assertDontSee($family->mother_neighborhood)
            ->assertDontSee($family->father_full_name)
            ->assertDontSee(route('dashboard.orphans.show', $family));
    }

    public function test_only_administrators_manage_sponsors(): void
    {
        $sponsor = Sponsor::factory()->create();

        foreach ([User::ROLE_EDITOR, User::ROLE_VIEWER] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->get(route('dashboard.sponsors.index'))->assertForbidden();
            $this->actingAs($user)->get(route('dashboard.sponsors.show', $sponsor))->assertForbidden();
            $this->actingAs($user)->post(route('dashboard.sponsors.store'), ['name' => 'كافل', 'preferred_currency' => 'USD'])->assertForbidden();
            $this->actingAs($user)->get(route('dashboard'))->assertDontSee('href="'.route('dashboard.sponsors.index').'"', false);
        }

        $this->assertSame(1, Sponsor::count());
        $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertSee('href="'.route('dashboard.sponsors.index').'"', false);
    }

    public function test_a_sponsor_added_with_an_email_gets_an_account_and_a_password_link(): void
    {
        Notification::fake();
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('dashboard.sponsors.store'), [
            'name' => 'خالد الحارثي',
            'email' => 'khaled@example.com',
            'preferred_currency' => 'SAR',
        ])->assertSessionHas('invitation_link');

        $sponsor = Sponsor::sole();
        $this->assertSame('SPN-'.str_pad((string) $sponsor->id, 4, '0', STR_PAD_LEFT), $sponsor->code);
        $this->assertSame(User::ROLE_SPONSOR, $sponsor->user->role);
        $this->assertSame('khaled@example.com', $sponsor->user->email);
        $this->assertSame($admin->id, $sponsor->created_by);
        Notification::assertSentTo($sponsor->user, SponsorInvitation::class, fn (SponsorInvitation $invitation): bool => $invitation->link === session('invitation_link'));

        $this->actingAs($admin)->get(route('dashboard.sponsors.show', $sponsor))->assertSee(session('invitation_link'), false);
    }

    public function test_a_sponsor_added_without_an_email_has_no_account(): void
    {
        Notification::fake();

        $this->actingAs(User::factory()->create())->post(route('dashboard.sponsors.store'), [
            'name' => 'أبو محمد',
            'preferred_currency' => 'USD',
        ])->assertSessionHasNoErrors();

        $this->assertNull(Sponsor::sole()->user_id);
        $this->assertSame(1, User::count());
        Notification::assertNothingSent();
    }

    public function test_editing_the_email_moves_or_closes_the_sponsor_account(): void
    {
        Notification::fake();
        $admin = User::factory()->create();
        $sponsor = Sponsor::factory()->withAccount()->create();
        $user = $sponsor->user;
        $details = ['name' => 'خالد الحارثي', 'preferred_currency' => 'USD'];

        $this->actingAs($admin)->put(route('dashboard.sponsors.update', $sponsor), [...$details, 'email' => 'new@example.com'])->assertSessionHasNoErrors();

        $this->assertSame('new@example.com', $user->refresh()->email);
        $this->assertSame('خالد الحارثي', $user->name);
        Notification::assertNothingSent();

        $this->actingAs($admin)->put(route('dashboard.sponsors.update', $sponsor), [...$details, 'email' => ''])->assertSessionHasNoErrors();

        $this->assertModelMissing($user);
        $this->assertNull($sponsor->refresh()->user_id);
    }

    public function test_a_sponsor_email_cannot_belong_to_another_account(): void
    {
        $admin = User::factory()->create(['email' => 'team@bader.org']);

        $this->actingAs($admin)->post(route('dashboard.sponsors.store'), [
            'name' => 'خالد الحارثي',
            'email' => 'team@bader.org',
            'preferred_currency' => 'USD',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseEmpty('sponsors');
        $this->assertSame(User::ROLE_ADMIN, $admin->refresh()->role);
    }

    public function test_a_sponsor_with_sponsorship_history_is_never_deleted(): void
    {
        $admin = User::factory()->create();
        $withHistory = Sponsorship::factory()->ended()->create()->sponsor;
        $withoutHistory = Sponsor::factory()->withAccount()->create();
        $account = $withoutHistory->user;

        $this->actingAs($admin)->delete(route('dashboard.sponsors.destroy', $withHistory))->assertSessionHasErrors('sponsor');
        $this->assertModelExists($withHistory);

        $this->actingAs($admin)->delete(route('dashboard.sponsors.destroy', $withoutHistory))->assertRedirect(route('dashboard.sponsors.index'));
        $this->assertModelMissing($withoutHistory);
        $this->assertModelMissing($account);
    }

    public function test_the_password_link_lets_the_sponsor_choose_a_password_and_sign_in(): void
    {
        Notification::fake();
        $this->actingAs(User::factory()->create())->post(route('dashboard.sponsors.store'), [
            'name' => 'خالد الحارثي',
            'email' => 'khaled@example.com',
            'preferred_currency' => 'USD',
        ]);
        $token = basename(parse_url(session('invitation_link'), PHP_URL_PATH));
        auth()->logout();

        $this->get(session('invitation_link'))->assertOk()->assertSee('khaled@example.com');

        $this->post(route('password.store'), ['token' => 'wrong-token', 'email' => 'khaled@example.com', 'password' => 'a-strong-pass-123', 'password_confirmation' => 'a-strong-pass-123'])
            ->assertSessionHasErrors('email');

        $this->post(route('password.store'), ['token' => $token, 'email' => 'khaled@example.com', 'password' => 'a-strong-pass-123', 'password_confirmation' => 'a-strong-pass-123'])
            ->assertRedirect(route('login'));

        $this->post(route('login.store'), ['email' => 'khaled@example.com', 'password' => 'a-strong-pass-123'])
            ->assertRedirect(route('sponsor.home'));
    }
}
