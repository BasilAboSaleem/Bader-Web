<?php

namespace Tests\Feature;

use App\Models\OrphanFamily;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrphanRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_visitor_registers_a_widow_family_that_waits_privately_for_review(): void
    {
        $this->get(route('home'))->assertSee('href="'.route('orphan-registration').'"', false);

        $this->get(route('orphan-registration'))
            ->assertOk()
            ->assertSee('name="documents[mother_id]"', false)
            ->assertSee(__('orphans.area.nuseirat_camp'));

        $this->post(route('orphan-registration.store'), $this->widowFamilyInput())
            ->assertRedirect(route('orphan-registration'))
            ->assertSessionHas('orphan_registered', true);

        $family = OrphanFamily::sole();
        $this->assertSame(OrphanFamily::REVIEW_PENDING, $family->review_status);
        $this->assertSame(OrphanFamily::SOURCE_WEBSITE, $family->source);
        $this->assertSame('ORP-0001', $family->code);
        $this->assertSame('فاطمة أحمد حسن النجار', $family->mother_full_name);
        $this->assertSame(['أحمد محمد سالم أبو عيد', 'مريم محمد سالم أبو عيد'], $family->orphans()->pluck('full_name')->all());

        $this->assertStringEndsWith('.jpg', $family->documents['mother_id']);
        $this->assertStringEndsWith('.pdf', $family->documents['martyr_certificate']);
        foreach (OrphanFamily::DOCUMENTS as $document) {
            Storage::disk('local')->assertExists($family->documents[$document]);
        }

        $this->withSession(['orphan_registered' => true])
            ->get(route('orphan-registration'))
            ->assertSee(__('orphan_registration.success_title'))
            ->assertDontSee('name="documents[mother_id]"', false)
            ->assertDontSee('فاطمة');

        $this->get(route('sponsorship'))
            ->assertOk()
            ->assertDontSee('فاطمة')
            ->assertDontSee('مريم');
    }

    public function test_registration_is_refused_without_every_document_or_with_a_file_over_one_megabyte(): void
    {
        $input = $this->widowFamilyInput();
        unset($input['documents']['martyr_certificate']);
        $input['documents']['mother_id'] = UploadedFile::fake()->image('id.jpg')->size(1500);

        $this->post(route('orphan-registration.store'), $input)
            ->assertSessionHasErrors(['documents.martyr_certificate', 'documents.mother_id']);

        $this->assertDatabaseCount('orphan_families', 0);
    }

    public function test_mother_details_are_required_only_when_sponsoring_a_widow_with_her_children(): void
    {
        $widowInput = $this->widowFamilyInput();
        unset($widowInput['mother_full_name'], $widowInput['mother_national_id'], $widowInput['mother_residence_area']);

        $this->post(route('orphan-registration.store'), $widowInput)
            ->assertSessionHasErrors(['mother_full_name', 'mother_national_id', 'mother_residence_area']);

        $orphanInput = [
            ...$this->widowFamilyInput(),
            'type' => OrphanFamily::TYPE_ORPHAN,
            'contact_name' => 'عبد الله سالم أبو عيد',
            'contact_relation' => 'العم',
            'contact_phone' => '0599123456',
        ];

        $this->post(route('orphan-registration.store'), $orphanInput)->assertSessionHasNoErrors();

        $family = OrphanFamily::sole();
        $this->assertSame(OrphanFamily::TYPE_ORPHAN, $family->type);
        $this->assertSame('0599123456', $family->contact_phone);
        $this->assertNull($family->mother_full_name);
        $this->assertNull($family->mother_national_id);
    }

    public function test_resending_the_same_form_on_a_slow_connection_saves_the_family_once(): void
    {
        $this->get(route('orphan-registration'))->assertSee('name="submission_token"', false);

        $token = (string) Str::uuid();

        foreach (range(1, 2) as $attempt) {
            $this->post(route('orphan-registration.store'), [...$this->widowFamilyInput(), 'submission_token' => $token])
                ->assertRedirect(route('orphan-registration'))
                ->assertSessionHas('orphan_registered', true);
        }

        $this->assertDatabaseCount('orphan_families', 1);
        $this->assertDatabaseCount('orphans', 2);
    }

    public function test_bots_that_fill_the_hidden_field_see_success_but_nothing_is_saved(): void
    {
        $this->post(route('orphan-registration.store'), [...$this->widowFamilyInput(), 'website' => 'https://spam.example'])
            ->assertRedirect(route('orphan-registration'))
            ->assertSessionHas('orphan_registered', true);

        $this->assertDatabaseCount('orphan_families', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_a_busy_shared_network_gets_a_friendly_message_instead_of_an_error_page(): void
    {
        foreach (range(1, 30) as $attempt) {
            $this->post(route('orphan-registration.store'), ['type' => OrphanFamily::TYPE_ORPHAN])
                ->assertSessionDoesntHaveErrors('throttle');
        }

        $this->post(route('orphan-registration.store'), ['type' => OrphanFamily::TYPE_ORPHAN])
            ->assertRedirect(route('orphan-registration'))
            ->assertSessionHasErrors(['throttle' => __('orphan_registration.too_many')]);
    }

    /**
     * @return array<string, mixed>
     */
    private function widowFamilyInput(): array
    {
        return [
            'type' => OrphanFamily::TYPE_WIDOW_FAMILY,
            'mother_full_name' => 'فاطمة أحمد حسن النجار',
            'mother_national_id' => '401234567',
            'mother_birth_date' => '1990-04-12',
            'mother_residence_area' => 'nuseirat_camp',
            'mother_neighborhood' => 'بلوك 5',
            'mother_nearest_landmark' => 'مسجد القسام',
            'mother_birth_country' => 'فلسطين',
            'mother_birth_place' => 'غزة',
            'mother_nationality' => 'فلسطينية',
            'mother_phone' => '0597123456',
            'father_full_name' => 'محمد سالم خليل أبو عيد',
            'father_death_date' => '2024-02-10',
            'father_death_cause' => 'استشهد في قصف',
            'orphans' => [
                ['full_name' => 'أحمد محمد سالم أبو عيد', 'birth_date' => '2015-06-01', 'national_id' => '412345678', 'education_stage' => 'primary', 'health_status' => 'healthy'],
                ['full_name' => 'مريم محمد سالم أبو عيد', 'birth_date' => '2018-09-15', 'national_id' => '423456789', 'education_stage' => 'kindergarten', 'health_status' => 'chronic_illness', 'health_notes' => 'ربو'],
            ],
            'documents' => [
                'father_death_certificate' => UploadedFile::fake()->image('death.png', 1200, 900),
                'martyr_certificate' => UploadedFile::fake()->createWithContent('martyr.pdf', "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF"),
                'mother_id' => UploadedFile::fake()->image('mother-id.jpg', 3000, 2000)->size(900),
            ],
        ];
    }
}
