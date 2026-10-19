<?php

namespace Tests\Feature;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\Sponsor;
use App\Models\SponsorRequest;
use App\Models\Sponsorship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SponsorshipPortalAndDisbursementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sponsor_can_open_case_file_print_and_submit_a_stop_request(): void
    {
        $sponsor = Sponsor::factory()->withAccount()->create();
        $family = OrphanFamily::factory()->approved()->widowFamily()->create([
            'mother_national_id' => '900111222',
            'mother_phone' => '0599000111',
            'mother_neighborhood' => 'الشجاعية',
        ]);
        $sponsorship = Sponsorship::factory()->forMother($family)->for($sponsor)->create([
            'monthly_amount' => 200,
            'currency' => 'USD',
            'renewal_type' => Sponsorship::RENEWAL_RECURRING,
        ]);

        $this->actingAs($sponsor->user)
            ->get(route('sponsor.show', $sponsorship))
            ->assertOk()
            ->assertSee('900111222')
            ->assertSee('0599000111')
            ->assertSee('الشجاعية')
            ->assertSee($family->father_full_name);

        $this->actingAs($sponsor->user)
            ->get(route('sponsor.print', $sponsorship))
            ->assertOk()
            ->assertSee(__('brand.name'))
            ->assertSee('900111222')
            ->assertSee(asset('brand/bader-logo.webp'), false);

        $this->actingAs($sponsor->user)
            ->post(route('sponsor.requests.store', $sponsorship), [
                'type' => SponsorRequest::TYPE_STOP,
                'message' => 'أحتاج إيقاف الكفالة.',
            ])
            ->assertRedirect(route('sponsor.show', $sponsorship));

        $this->assertDatabaseHas('sponsor_requests', [
            'sponsorship_id' => $sponsorship->id,
            'type' => SponsorRequest::TYPE_STOP,
            'status' => SponsorRequest::STATUS_PENDING,
        ]);
    }

    public function test_admin_approves_increase_request_and_records_disbursement_visible_to_sponsor(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $sponsor = Sponsor::factory()->withAccount()->create();
        $family = OrphanFamily::factory()->approved()->widowFamily()->create();
        $sponsorship = Sponsorship::factory()->forMother($family)->for($sponsor)->create([
            'monthly_amount' => 100,
            'currency' => 'USD',
        ]);

        $request = SponsorRequest::factory()->create([
            'sponsorship_id' => $sponsorship->id,
            'sponsor_id' => $sponsor->id,
            'type' => SponsorRequest::TYPE_INCREASE,
            'requested_amount' => 150,
            'currency' => 'USD',
        ]);

        $this->actingAs($admin)
            ->patch(route('dashboard.sponsor-requests.update', $request), [
                'decision' => SponsorRequest::STATUS_APPROVED,
            ])
            ->assertRedirect(route('dashboard.sponsor-requests.index'));

        $this->assertNotNull($sponsorship->fresh()->ended_at);
        $active = Sponsorship::query()->where('orphan_family_id', $family->id)->active()->first();
        $this->assertSame('150.00', $active->monthly_amount);

        $this->actingAs($admin)
            ->post(route('dashboard.disbursements.store'), [
                'period_year' => 2026,
                'period_month' => 9,
                'paid_on' => '2026-09-28',
                'rows' => [
                    [
                        'sponsorship_id' => $active->id,
                        'amount' => 150,
                        'currency' => 'USD',
                        'notes' => 'صرف سبتمبر',
                    ],
                ],
            ])
            ->assertRedirect(route('dashboard.disbursements.index', ['year' => 2026, 'month' => 9]));

        $this->assertDatabaseHas('sponsorship_disbursements', [
            'sponsorship_id' => $active->id,
            'period_year' => 2026,
            'period_month' => 9,
            'amount' => 150,
        ]);

        $this->actingAs($sponsor->user)
            ->get(route('sponsor.finance', $active))
            ->assertOk()
            ->assertSee('2026-09')
            ->assertSee('صرف سبتمبر');

        $this->actingAs($admin)
            ->get(route('dashboard.orphans.print', $family))
            ->assertOk()
            ->assertSee($family->code)
            ->assertSee(__('brand.name'));

        $this->actingAs($admin)
            ->get(route('dashboard.reports.finance', ['year' => 2026, 'month' => 9]))
            ->assertOk()
            ->assertSee($family->mother_full_name)
            ->assertSee(__('sponsors.report.col.month_amount'));
    }

    public function test_sponsor_cannot_see_another_sponsors_case_or_disbursement_tools(): void
    {
        $sponsor = Sponsor::factory()->withAccount()->create();
        $other = Sponsorship::factory()->create();

        $this->actingAs($sponsor->user)->get(route('sponsor.show', $other))->assertNotFound();
        $this->actingAs($sponsor->user)->get(route('dashboard.disbursements.index'))->assertForbidden();
        $this->actingAs($sponsor->user)->get(route('dashboard.orphans.print', $other->family))->assertForbidden();
    }

    public function test_assigning_sponsorship_stores_renewal_fields(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $sponsor = Sponsor::factory()->create();
        $family = OrphanFamily::factory()->approved()->create(['type' => OrphanFamily::TYPE_ORPHAN]);
        $orphan = Orphan::factory()->for($family, 'family')->create();

        $this->actingAs($admin)
            ->patch(route('dashboard.orphans.sponsorship', $family), [
                'orphans' => [
                    $orphan->id => [
                        'sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED,
                        'sponsor_id' => $sponsor->id,
                        'monthly_amount' => 80,
                        'currency' => 'ILS',
                        'started_at' => '2026-01-01',
                        'renewal_type' => Sponsorship::RENEWAL_ONE_TIME,
                        'planned_ended_at' => '2026-06-30',
                    ],
                ],
            ])
            ->assertRedirect(route('dashboard.orphans.show', $family));

        $created = Sponsorship::query()->where('orphan_id', $orphan->id)->active()->first();

        $this->assertNotNull($created);
        $this->assertSame(Sponsorship::RENEWAL_ONE_TIME, $created->renewal_type);
        $this->assertSame('2026-06-30', $created->planned_ended_at->toDateString());
        $this->assertSame('80.00', $created->monthly_amount);
    }
}
