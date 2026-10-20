<?php

namespace Tests\Feature;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\User;
use App\Support\OrphanFamilyInput;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardOrphansTest extends TestCase
{
    use RefreshDatabase;

    public function test_orphan_files_are_closed_to_guests_and_viewers(): void
    {
        $family = OrphanFamily::factory()->create();
        $viewer = User::factory()->create(['role' => User::ROLE_VIEWER]);

        $this->get(route('dashboard.orphans.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard.orphans.document', [$family, 'mother_id']))->assertRedirect(route('login'));

        $this->actingAs($viewer)->get(route('dashboard.orphans.show', $family))->assertForbidden();
        $this->actingAs($viewer)->get(route('dashboard.orphans.document', [$family, 'mother_id']))->assertForbidden();
        $this->actingAs($viewer)->get(route('dashboard'))->assertDontSee('href="'.route('dashboard.orphans.index').'"', false);
    }

    public function test_admin_reviews_a_website_registration_before_visitors_see_it(): void
    {
        $admin = User::factory()->create();
        $family = OrphanFamily::factory()->create();
        Orphan::factory()->for($family, 'family')->create([
            'full_name' => 'يوسف محمد سالم أبو عيد',
            'national_id' => '412345678',
            'birth_date' => now()->subYears(9)->toDateString(),
        ]);

        $this->get(route('sponsorship'))->assertDontSee('يوسف');

        $this->actingAs($admin)->get(route('dashboard.orphans.index'))
            ->assertOk()
            ->assertViewHas('currentStatus', OrphanFamily::REVIEW_PENDING)
            ->assertSee($family->code)
            ->assertSee(__('orphans.status.pending_review'))
            ->assertDontSee(__('orphans.status.available'));

        $this->actingAs($admin)->get(route('dashboard.orphans.show', $family))
            ->assertOk()
            ->assertSee('يوسف محمد سالم أبو عيد')
            ->assertSee('412345678')
            ->assertSee(route('dashboard.orphans.document', [$family, 'martyr_certificate']), false);

        $this->actingAs($admin)->patch(route('dashboard.orphans.review', $family), ['review_status' => OrphanFamily::REVIEW_APPROVED])
            ->assertRedirect(route('dashboard.orphans.show', $family));

        $family->refresh();
        $this->assertTrue($family->isApproved());
        $this->assertTrue($family->reviewer->is($admin));

        $this->get(route('sponsorship'))
            ->assertOk()
            ->assertSee('يوسف')
            ->assertSee(__('sponsorship.age_years', ['age' => 9]))
            ->assertDontSee('يوسف محمد')
            ->assertDontSee('412345678')
            ->assertDontSee($family->father_full_name);

        $this->actingAs($admin)->patch(route('dashboard.orphans.review', $family), [
            'review_status' => OrphanFamily::REVIEW_REJECTED,
            'review_notes' => 'الوثائق غير واضحة',
        ]);

        $this->assertSame('الوثائق غير واضحة', $family->refresh()->review_notes);
        $this->get(route('sponsorship'))->assertDontSee('يوسف');
    }

    public function test_attachments_are_served_only_from_the_private_disk_for_known_documents(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('orphan-documents/test/mother-id.jpg', 'image-bytes');
        $admin = User::factory()->create();
        $family = OrphanFamily::factory()->create();

        $this->actingAs($admin)->get(route('dashboard.orphans.document', [$family, 'mother_id']))
            ->assertOk()
            ->assertStreamedContent('image-bytes');

        $this->actingAs($admin)->get(route('dashboard.orphans.document', [$family, 'passport']))->assertNotFound();
    }

    public function test_optional_portrait_is_shrunk_kept_on_later_edits_and_stays_private(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('dashboard.orphans.store'), [
            'type' => OrphanFamily::TYPE_ORPHAN,
            'contact_name' => 'عبد الله سالم أبو عيد',
            'contact_relation' => 'العم',
            'contact_phone' => '0599123456',
            'father_full_name' => 'محمد سالم خليل أبو عيد',
            'father_death_date' => '2024-02-10',
            'father_death_cause' => 'استشهد في قصف',
            'orphans' => [[
                'full_name' => 'أحمد محمد سالم أبو عيد',
                'birth_date' => '2015-06-01',
                'national_id' => '412345678',
                'education_stage' => 'primary',
                'health_status' => 'healthy',
                'photo' => UploadedFile::fake()->image('ahmad.jpg', 2000, 1500),
            ]],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $family = OrphanFamily::sole();
        $orphan = $family->orphans()->first();
        $stored = $orphan->photo;
        [$width, $height] = getimagesize(Storage::disk('local')->path($stored));

        $this->assertStringEndsWith('.jpg', $stored);
        $this->assertLessThanOrEqual(OrphanFamilyInput::PHOTO_MAX_EDGE, max($width, $height));
        $this->assertLessThan(80 * 1024, Storage::disk('local')->size($stored));

        auth()->logout();
        $this->get(route('dashboard.orphans.photo', [$family, $orphan]))->assertRedirect(route('login'));
        $this->actingAs($admin)->get(route('dashboard.orphans.photo', [$family, $orphan]))->assertOk();
        $this->actingAs($admin)->get(route('dashboard.orphans.show', $family))
            ->assertSee(route('dashboard.orphans.photo', [$family, $orphan]), false)
            ->assertSee('data-lightbox="orphan-portraits"', false);
        $this->actingAs($admin)->get(route('dashboard.orphans.edit', $family))
            ->assertSee('name="orphans[0][photo]"', false)
            ->assertSee(route('dashboard.orphans.photo', [$family, $orphan]), false);

        $other = Orphan::factory()->create();
        $this->actingAs($admin)->get(route('dashboard.orphans.photo', [$family, $other]))->assertNotFound();

        $this->actingAs($admin)->put(route('dashboard.orphans.update', $family), [
            'type' => OrphanFamily::TYPE_ORPHAN,
            'contact_name' => 'عبد الله سالم أبو عيد',
            'contact_relation' => 'العم',
            'contact_phone' => '0599123456',
            'father_full_name' => 'محمد سالم خليل أبو عيد',
            'father_death_date' => '2024-02-10',
            'father_death_cause' => 'استشهد في قصف',
            'orphans' => [[
                'id' => $orphan->id,
                'full_name' => 'أحمد محمد سالم أبو عيد',
                'birth_date' => '2015-06-01',
                'national_id' => '412345678',
                'education_stage' => 'primary',
                'health_status' => 'healthy',
            ]],
        ])->assertRedirect(route('dashboard.orphans.show', $family));

        $this->assertSame($stored, $orphan->refresh()->photo);
        Storage::disk('local')->assertExists($stored);

        $this->actingAs($admin)->post(route('dashboard.orphans.store'), [
            'type' => OrphanFamily::TYPE_ORPHAN,
            'contact_name' => 'عبد الله سالم أبو عيد',
            'contact_relation' => 'العم',
            'contact_phone' => '0599123456',
            'father_full_name' => 'محمد سالم خليل أبو عيد',
            'father_death_date' => '2024-02-10',
            'father_death_cause' => 'استشهد في قصف',
            'orphans' => [[
                'full_name' => 'سلمى محمد سالم أبو عيد',
                'birth_date' => '2017-03-20',
                'national_id' => '423456789',
                'education_stage' => 'primary',
                'health_status' => 'healthy',
                'photo' => UploadedFile::fake()->image('big.jpg')->size(OrphanFamilyInput::PHOTO_MAX_KB + 200),
            ]],
        ])->assertSessionHasErrors(['orphans.0.photo']);

        $this->actingAs($admin)->delete(route('dashboard.orphans.destroy', $family));

        Storage::disk('local')->assertMissing($stored);
    }

    public function test_admin_adds_a_file_manually_then_edits_its_orphans(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create();

        $this->actingAs($admin)->get(route('dashboard.orphans.create'))->assertOk();

        $this->actingAs($admin)->post(route('dashboard.orphans.store'), [
            'type' => OrphanFamily::TYPE_ORPHAN,
            'contact_name' => 'عبد الله سالم أبو عيد',
            'contact_relation' => 'العم',
            'contact_phone' => '0599123456',
            'father_full_name' => 'محمد سالم خليل أبو عيد',
            'father_death_date' => '2024-02-10',
            'father_death_cause' => 'استشهد في قصف',
            'orphans' => [
                ['full_name' => 'أحمد محمد سالم أبو عيد', 'birth_date' => '2015-06-01', 'national_id' => '412345678', 'education_stage' => 'primary', 'health_status' => 'healthy'],
                ['full_name' => 'سلمى محمد سالم أبو عيد', 'birth_date' => '2017-03-20', 'national_id' => '423456789', 'education_stage' => 'primary', 'health_status' => 'healthy'],
            ],
        ])->assertSessionHasNoErrors();

        $family = OrphanFamily::sole();
        $this->assertSame(OrphanFamily::REVIEW_PENDING, $family->review_status);
        $this->assertSame(OrphanFamily::SOURCE_DASHBOARD, $family->source);
        $this->get(route('sponsorship'))->assertDontSee('سلمى');
        [$ahmad, $salma] = $family->orphans()->get()->all();

        $this->actingAs($admin)->get(route('dashboard.orphans.edit', $family))->assertOk()->assertSee('سلمى محمد سالم أبو عيد');

        $this->actingAs($admin)->put(route('dashboard.orphans.update', $family), [
            'type' => OrphanFamily::TYPE_ORPHAN,
            'contact_name' => 'عبد الله سالم أبو عيد',
            'contact_relation' => 'العم',
            'contact_phone' => '0599123456',
            'father_full_name' => 'محمد سالم خليل أبو عيد',
            'father_death_date' => '2024-02-10',
            'father_death_cause' => 'استشهد في قصف',
            'orphans' => [
                ['id' => $ahmad->id, 'full_name' => 'أحمد محمد سالم أبو عيد', 'birth_date' => '2015-06-01', 'national_id' => '412345678', 'education_stage' => 'preparatory', 'health_status' => 'healthy'],
                ['full_name' => 'عمر محمد سالم أبو عيد', 'birth_date' => '2019-01-05', 'national_id' => '434567890', 'education_stage' => 'under_school_age', 'health_status' => 'healthy'],
            ],
            'documents' => ['mother_id' => UploadedFile::fake()->image('mother-id.jpg')],
        ])->assertRedirect(route('dashboard.orphans.show', $family));

        $this->assertSame('preparatory', $ahmad->refresh()->education_stage);
        $this->assertModelMissing($salma);
        $this->assertSame(['أحمد محمد سالم أبو عيد', 'عمر محمد سالم أبو عيد'], $family->orphans()->pluck('full_name')->all());
        Storage::disk('local')->assertExists($family->refresh()->documents['mother_id']);

        $this->actingAs($admin)->delete(route('dashboard.orphans.destroy', $family));

        $this->assertModelMissing($family);
        Storage::disk('local')->assertMissing('orphan-documents/'.$family->id);
    }

    public function test_sponsorship_cannot_be_recorded_before_the_file_is_approved(): void
    {
        $admin = User::factory()->create();
        $sponsor = Sponsor::factory()->create();
        $family = OrphanFamily::factory()->widowFamily()->create();
        Orphan::factory()->for($family, 'family')->create();

        $this->actingAs($admin)->get(route('dashboard.orphans.show', $family))
            ->assertSee(__('orphans.sponsorship_needs_approval'))
            ->assertDontSee('name="sponsor_id"', false);

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), [
            'sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED,
            'sponsor_id' => $sponsor->id,
            'currency' => 'USD',
            'started_at' => '2026-10-01',
            'mother_amount' => 150,
        ])->assertSessionHasErrors('sponsorship');

        $this->assertTrue($family->refresh()->isAvailable());
        $this->assertDatabaseEmpty('sponsorships');
    }

    public function test_editors_see_sponsorships_but_only_administrators_record_them(): void
    {
        $editor = User::factory()->create(['role' => User::ROLE_EDITOR]);
        $sponsorship = Sponsorship::factory()->create(['monthly_amount' => 75]);
        $family = $sponsorship->family;
        $family->update(['review_status' => OrphanFamily::REVIEW_APPROVED]);

        $this->actingAs($editor)->get(route('dashboard.orphans.show', $family))
            ->assertOk()
            ->assertSee($sponsorship->sponsor->name)
            ->assertSee('$75.00')
            ->assertSee(__('sponsors.managed_by_admins'))
            ->assertDontSee('name="sponsor_id"', false)
            ->assertDontSee(route('dashboard.sponsors.show', $sponsorship->sponsor));

        $this->actingAs($editor)->patch(route('dashboard.orphans.sponsorship', $family), [
            'orphans' => [$sponsorship->orphan_id => ['sponsorship_status' => OrphanFamily::SPONSORSHIP_AVAILABLE]],
        ])->assertForbidden();

        $this->assertTrue($sponsorship->refresh()->isActive());
    }

    public function test_a_widow_case_is_sponsored_with_one_total_instead_of_a_per_person_split(): void
    {
        $admin = User::factory()->create();
        $sponsor = Sponsor::factory()->create(['name' => 'خالد الحارثي']);
        $family = OrphanFamily::factory()->approved()->widowFamily()->create();
        [$ahmad, $salma] = Orphan::factory()->for($family, 'family')->count(2)->create()->all();
        $terms = ['sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED, 'sponsor_id' => $sponsor->id, 'currency' => 'ILS', 'started_at' => '2026-10-01'];

        $this->actingAs($admin)->get(route('dashboard.orphans.show', $family))
            ->assertSee('name="sponsorship_amount"', false)
            ->assertDontSee('name="mother_amount"', false);

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), ['sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED])
            ->assertSessionHasErrors(['sponsor_id', 'currency', 'started_at', 'sponsorship_amount']);
        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), [...$terms, 'sponsor_id' => 9999, 'sponsorship_amount' => 450])
            ->assertSessionHasErrors('sponsor_id');
        $this->assertTrue($family->refresh()->isAvailable());

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), [
            ...$terms,
            'sponsorship_amount' => 450,
        ])->assertRedirect(route('dashboard.orphans.show', $family));

        $this->assertSame(OrphanFamily::SPONSORSHIP_SPONSORED, $family->refresh()->sponsorship_status);
        $this->assertSame('450.00', $family->sponsorship_amount);
        $this->assertSame(['family' => '450.00'], $family->activeSponsorships()->pluck('monthly_amount', 'beneficiary_type')->all());
        $this->assertTrue($ahmad->refresh()->isAvailable());
        $this->assertTrue($salma->refresh()->isAvailable());

        $this->actingAs($admin)->get(route('dashboard.orphans.print', $family))
            ->assertOk()
            ->assertSee('450.00')
            ->assertDontSee(__('sponsors.column.beneficiary'));

        $this->get(route('sponsorship'))
            ->assertOk()
            ->assertSee(__('sponsorship.status.sponsored'))
            ->assertDontSee('خالد الحارثي')
            ->assertDontSee('450.00');

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), [
            ...$terms,
            'sponsorship_status' => OrphanFamily::SPONSORSHIP_AVAILABLE,
        ]);

        $this->assertTrue($family->refresh()->isAvailable());
        $this->assertNull($family->sponsorship_amount);
        $this->assertSame(0, $family->activeSponsorships()->count());
        $this->assertSame(1, $family->sponsorships()->whereNotNull('ended_at')->count());
    }

    public function test_an_existing_per_person_split_still_prints_as_one_total(): void
    {
        $admin = User::factory()->create();
        $family = OrphanFamily::factory()->approved()->widowFamily()->create();
        $orphan = Orphan::factory()->for($family, 'family')->create();
        Sponsorship::factory()->forMother($family)->create(['monthly_amount' => 300, 'currency' => 'ILS']);
        Sponsorship::factory()->for($orphan)->create([
            'orphan_family_id' => $family->id,
            'monthly_amount' => 150,
            'currency' => 'ILS',
        ]);

        $this->actingAs($admin)->get(route('dashboard.orphans.print', $family))
            ->assertOk()
            ->assertSee('450.00')
            ->assertDontSee('300.00')
            ->assertDontSee('150.00')
            ->assertDontSee(__('sponsors.column.beneficiary'));
    }

    public function test_prepaid_sponsorship_requires_an_end_date_and_the_report_says_it_was_paid_ahead(): void
    {
        $admin = User::factory()->create();
        $sponsor = Sponsor::factory()->create();
        $family = OrphanFamily::factory()->approved()->widowFamily()->create();
        Orphan::factory()->for($family, 'family')->create();
        $terms = [
            'sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED,
            'sponsor_id' => $sponsor->id,
            'currency' => 'ILS',
            'started_at' => '2026-01-01',
            'renewal_type' => Sponsorship::RENEWAL_PREPAID,
            'sponsorship_amount' => 600,
        ];

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), $terms)
            ->assertSessionHasErrors('planned_ended_at');

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), [
            ...$terms,
            'planned_ended_at' => '2026-12-31',
        ])->assertRedirect(route('dashboard.orphans.show', $family));

        $sponsorship = Sponsorship::active()->sole();
        $this->assertTrue($sponsorship->isPrepaid());
        $this->assertSame('2026-01-01', $sponsorship->started_at->toDateString());
        $this->assertSame('2026-12-31', $sponsorship->planned_ended_at->toDateString());

        $period = __('sponsors.prepaid_period', ['from' => '2026-01-01', 'until' => '2026-12-31']);

        $this->actingAs($admin)->get(route('dashboard.orphans.print', $family))
            ->assertOk()
            ->assertSee(__('sponsors.renewal.prepaid'))
            ->assertSee($period);

        $this->actingAs($admin)->get(route('dashboard.orphans.finance', $family))
            ->assertOk()
            ->assertSee($period);
    }

    public function test_changing_the_amount_keeps_the_earlier_sponsorship_as_history(): void
    {
        $admin = User::factory()->create();
        $sponsorship = Sponsorship::factory()->create(['monthly_amount' => 50, 'started_at' => '2026-01-01']);
        $family = $sponsorship->family;
        $family->update(['review_status' => OrphanFamily::REVIEW_APPROVED]);
        $row = ['sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED, 'sponsor_id' => $sponsorship->sponsor_id, 'currency' => 'USD'];

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), [
            'orphans' => [$sponsorship->orphan_id => [...$row, 'monthly_amount' => 50, 'started_at' => '2026-01-01']],
        ]);
        $this->assertSame(1, Sponsorship::count());

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), [
            'orphans' => [$sponsorship->orphan_id => [...$row, 'monthly_amount' => 80, 'started_at' => '2026-07-01']],
        ])->assertSessionHasNoErrors();

        $sponsorship->refresh();
        $this->assertSame('2026-06-30', $sponsorship->ended_at->toDateString());
        $this->assertSame($admin->id, $sponsorship->ended_by);
        $this->assertSame('80.00', Sponsorship::active()->sole()->monthly_amount);
    }

    public function test_orphan_files_are_sponsored_child_by_child_within_the_same_file_only(): void
    {
        $admin = User::factory()->create();
        $sponsor = Sponsor::factory()->create();
        $family = OrphanFamily::factory()->approved()->create();
        [$ahmad, $salma] = Orphan::factory()->for($family, 'family')->count(2)->create()->all();
        $otherOrphan = Orphan::factory()->create();
        $row = ['sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED, 'sponsor_id' => $sponsor->id, 'monthly_amount' => 50, 'currency' => 'SAR', 'started_at' => '2026-10-01'];

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), [
            'orphans' => [$ahmad->id => ['sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED]],
        ])->assertSessionHasErrors(["orphans.{$ahmad->id}.sponsor_id", "orphans.{$ahmad->id}.monthly_amount"]);

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), [
            'orphans' => [
                $ahmad->id => $row,
                $salma->id => ['sponsorship_status' => OrphanFamily::SPONSORSHIP_AVAILABLE],
                $otherOrphan->id => $row,
            ],
        ])->assertRedirect(route('dashboard.orphans.show', $family));

        $this->assertSame('SAR', $ahmad->activeSponsorship()->value('currency'));
        $this->assertFalse($ahmad->refresh()->isAvailable());
        $this->assertTrue($salma->refresh()->isAvailable());
        $this->assertTrue($otherOrphan->refresh()->isAvailable());
        $this->assertSame(1, Sponsorship::count());
        $this->assertSame(OrphanFamily::STATUS_PARTIALLY_SPONSORED, $family->refresh()->currentStatus());
    }

    public function test_sponsorship_history_cannot_be_deleted_with_its_file_or_child(): void
    {
        $admin = User::factory()->create();
        $sponsorship = Sponsorship::factory()->ended()->create();
        $family = $sponsorship->family;
        $fileInput = [
            'type' => OrphanFamily::TYPE_ORPHAN,
            'contact_name' => $family->contact_name,
            'contact_relation' => $family->contact_relation,
            'contact_phone' => $family->contact_phone,
            'father_full_name' => $family->father_full_name,
            'father_death_date' => $family->father_death_date->toDateString(),
            'father_death_cause' => $family->father_death_cause,
            'orphans' => [
                ['full_name' => 'عمر محمد سالم أبو عيد', 'birth_date' => '2019-01-05', 'national_id' => '434567890', 'education_stage' => 'under_school_age', 'health_status' => 'healthy'],
            ],
        ];

        $this->actingAs($admin)->put(route('dashboard.orphans.update', $family), $fileInput)
            ->assertSessionHasErrors('orphans');
        $this->assertModelExists($sponsorship->orphan);

        $this->actingAs($admin)->delete(route('dashboard.orphans.destroy', $family))
            ->assertSessionHasErrors('sponsorship');
        $this->assertModelExists($family);
    }
}
