<?php

namespace Tests\Feature;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\User;
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
            ->assertSee($family->code);

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
        $this->assertSame(OrphanFamily::REVIEW_APPROVED, $family->review_status);
        $this->assertSame(OrphanFamily::SOURCE_DASHBOARD, $family->source);
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

    public function test_sponsorship_status_changes_only_touch_orphans_of_the_same_file(): void
    {
        $admin = User::factory()->create();
        $family = OrphanFamily::factory()->approved()->create();
        $orphan = Orphan::factory()->for($family, 'family')->create();
        $otherOrphan = Orphan::factory()->create();

        $this->actingAs($admin)->patch(route('dashboard.orphans.sponsorship', $family), [
            'sponsorship_status' => OrphanFamily::SPONSORSHIP_AVAILABLE,
            'orphans' => [
                $orphan->id => OrphanFamily::SPONSORSHIP_SPONSORED,
                $otherOrphan->id => OrphanFamily::SPONSORSHIP_SPONSORED,
            ],
        ])->assertRedirect(route('dashboard.orphans.show', $family));

        $this->assertFalse($orphan->refresh()->isAvailable());
        $this->assertTrue($otherOrphan->refresh()->isAvailable());
    }
}
