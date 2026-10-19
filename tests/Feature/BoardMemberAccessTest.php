<?php

namespace Tests\Feature;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\SponsorshipDisbursement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardMemberAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_board_member_can_view_orphan_cases_and_monthly_finance_reports_only(): void
    {
        $board = User::factory()->create(['role' => User::ROLE_BOARD_MEMBER]);
        $family = OrphanFamily::factory()->approved()->widowFamily()->create([
            'mother_national_id' => '411122233',
            'mother_phone' => '0599111222',
        ]);
        $sponsorship = Sponsorship::factory()->forMother($family)->create(['monthly_amount' => 200]);
        SponsorshipDisbursement::factory()->create([
            'sponsorship_id' => $sponsorship->id,
            'period_year' => 2026,
            'period_month' => 9,
            'amount' => 200,
            'paid_on' => '2026-09-25',
        ]);

        $this->actingAs($board)->get(route('dashboard'))->assertRedirect(route('dashboard.orphans.index'));

        $this->actingAs($board)->get(route('dashboard.orphans.index'))->assertOk();
        $this->actingAs($board)->get(route('dashboard.orphans.show', $family))
            ->assertOk()
            ->assertSee('411122233')
            ->assertSee('0599111222')
            ->assertSee('2026-09')
            ->assertDontSee(route('dashboard.orphans.edit', $family), false);

        $this->actingAs($board)->get(route('dashboard.orphans.print', $family))
            ->assertOk()
            ->assertSee(__('sponsors.report.finance_case'))
            ->assertSee('2026-09');

        $this->actingAs($board)->get(route('dashboard.reports.finance', ['year' => 2026, 'month' => 9]))
            ->assertOk()
            ->assertSee(__('sponsors.report.col.widow_name'))
            ->assertSee($family->mother_full_name)
            ->assertSee('411122233')
            ->assertSee('0599111222');

        $this->actingAs($board)->get(route('dashboard.reports.finance.print', ['year' => 2026, 'month' => 9]))
            ->assertOk()
            ->assertSee(__('brand.name'));

        $this->actingAs($board)->get(route('dashboard.orphans.create'))->assertForbidden();
        $this->actingAs($board)->get(route('dashboard.orphans.edit', $family))->assertForbidden();
        $this->actingAs($board)->get(route('dashboard.disbursements.index'))->assertForbidden();
        $this->actingAs($board)->get(route('dashboard.sponsors.index'))->assertForbidden();
        $this->actingAs($board)->get(route('dashboard.programs.index'))->assertForbidden();
        $this->actingAs($board)->get(route('dashboard.settings.edit'))->assertForbidden();
    }

    public function test_super_admin_can_create_a_board_member_account(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($superAdmin)
            ->post(route('dashboard.users.store'), [
                'name' => 'عضو مجلس',
                'email' => 'board@bader.test',
                'password' => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
                'role' => User::ROLE_BOARD_MEMBER,
            ])
            ->assertRedirect(route('dashboard.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'board@bader.test',
            'role' => User::ROLE_BOARD_MEMBER,
        ]);
    }

    public function test_case_file_includes_all_monthly_disbursements_for_the_family(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $family = OrphanFamily::factory()->approved()->widowFamily()->create();
        $mother = Sponsorship::factory()->forMother($family)->create(['monthly_amount' => 150]);
        $orphan = Orphan::factory()->for($family, 'family')->create([
            'full_name' => 'أحمد اليتيم',
            'national_id' => '500111222',
            'sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED,
        ]);
        $child = Sponsorship::factory()->for(Sponsor::factory())->for($orphan)->create([
            'orphan_family_id' => $family->id,
            'monthly_amount' => 100,
        ]);

        SponsorshipDisbursement::factory()->create([
            'sponsorship_id' => $mother->id,
            'period_year' => 2026,
            'period_month' => 8,
            'amount' => 150,
        ]);
        SponsorshipDisbursement::factory()->create([
            'sponsorship_id' => $child->id,
            'period_year' => 2026,
            'period_month' => 8,
            'amount' => 100,
        ]);

        $this->actingAs($admin)->get(route('dashboard.orphans.show', $family))
            ->assertOk()
            ->assertSee(__('sponsors.report.finance_case'))
            ->assertSee(__('sponsors.report.approved_amount'))
            ->assertSee('2026-08')
            ->assertSee('أحمد اليتيم');

        $this->actingAs($admin)->get(route('dashboard.orphans.finance', $family))
            ->assertOk()
            ->assertSee(__('sponsors.report.received_total'))
            ->assertSee('2026-08');

        $this->actingAs($admin)->get(route('dashboard.reports.finance', ['year' => 2026, 'month' => 8]))
            ->assertOk()
            ->assertSee(__('sponsors.report.col.widow_name'))
            ->assertSee($family->mother_full_name)
            ->assertSee($family->mother_national_id);
    }
}
