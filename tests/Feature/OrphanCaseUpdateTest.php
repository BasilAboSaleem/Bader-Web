<?php

namespace Tests\Feature;

use App\Http\Controllers\OrphanCaseUpdateController;
use App\Models\Orphan;
use App\Models\OrphanFamily;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrphanCaseUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_visitor_opens_a_widow_file_by_mother_national_id_and_completes_missing_data(): void
    {
        $family = OrphanFamily::factory()->widowFamily()->create([
            'mother_national_id' => '401234567',
            'mother_phone' => '0597000000',
            'review_status' => OrphanFamily::REVIEW_APPROVED,
            'reviewed_at' => now(),
            'documents' => [],
        ]);
        $orphan = Orphan::factory()->for($family, 'family')->create([
            'full_name' => 'أحمد محمد سالم أبو عيد',
            'national_id' => '412345678',
        ]);

        $this->get(route('home'))->assertSee('href="'.route('orphan-case-update').'"', false);

        $this->get(route('orphan-case-update'))
            ->assertOk()
            ->assertSee(__('orphan_case_update.title'));

        $this->post(route('orphan-case-update.lookup'), ['national_id' => '401234567'])
            ->assertRedirect(route('orphan-case-update.edit'))
            ->assertSessionHas(OrphanCaseUpdateController::SESSION_FAMILY_ID, $family->id);

        $this->get(route('orphan-case-update.edit'))
            ->assertOk()
            ->assertSee('فاطمة')
            ->assertSee('name="documents[mother_id]"', false)
            ->assertDontSee(route('dashboard.orphans.document', [$family, 'mother_id']), false);

        $this->post(route('orphan-case-update.update'), $this->updateInput($family, $orphan, [
            'mother_phone' => '0597123456',
            'documents' => [
                'father_death_certificate' => UploadedFile::fake()->image('death.png', 800, 600),
                'martyr_certificate' => UploadedFile::fake()->createWithContent('martyr.pdf', "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF"),
                'mother_id' => UploadedFile::fake()->image('mother-id.jpg', 800, 600),
            ],
        ]))
            ->assertRedirect(route('orphan-case-update'))
            ->assertSessionHas('orphan_case_updated', true)
            ->assertSessionMissing(OrphanCaseUpdateController::SESSION_FAMILY_ID);

        $family->refresh();
        $this->assertSame(OrphanFamily::REVIEW_PENDING, $family->review_status);
        $this->assertNull($family->reviewed_at);
        $this->assertSame('0597123456', $family->mother_phone);
        $this->assertSame('401234567', $family->mother_national_id);

        foreach (OrphanFamily::DOCUMENTS as $document) {
            $this->assertTrue($family->hasDocument($document));
            Storage::disk('local')->assertExists($family->documents[$document]);
        }
    }

    public function test_visitor_can_open_a_file_using_an_orphan_national_id(): void
    {
        $family = OrphanFamily::factory()->create([
            'documents' => [
                'father_death_certificate' => 'orphan-documents/1/death.jpg',
                'martyr_certificate' => 'orphan-documents/1/martyr.jpg',
                'mother_id' => 'orphan-documents/1/mother.jpg',
            ],
        ]);
        Orphan::factory()->for($family, 'family')->create(['national_id' => '498765432']);

        $this->post(route('orphan-case-update.lookup'), ['national_id' => '498765432'])
            ->assertRedirect(route('orphan-case-update.edit'));

        $this->get(route('orphan-case-update.edit'))
            ->assertOk()
            ->assertSee($family->father_full_name);
    }

    public function test_unknown_national_id_stays_on_the_lookup_page(): void
    {
        $this->post(route('orphan-case-update.lookup'), ['national_id' => '400000000'])
            ->assertRedirect(route('orphan-case-update'))
            ->assertSessionHasErrors(['national_id' => __('orphan_case_update.not_found')]);
    }

    public function test_edit_page_requires_a_verified_session(): void
    {
        $this->get(route('orphan-case-update.edit'))
            ->assertRedirect(route('orphan-case-update'))
            ->assertSessionHasErrors(['national_id' => __('orphan_case_update.session_expired')]);
    }

    public function test_missing_certificates_are_required_when_the_file_has_none(): void
    {
        $family = OrphanFamily::factory()->widowFamily()->create([
            'mother_national_id' => '401234567',
            'documents' => [],
        ]);
        $orphan = Orphan::factory()->for($family, 'family')->create();

        $this->withSession([
            OrphanCaseUpdateController::SESSION_FAMILY_ID => $family->id,
            OrphanCaseUpdateController::SESSION_VERIFIED_AT => now()->getTimestamp(),
        ])->post(route('orphan-case-update.update'), $this->updateInput($family, $orphan, [
            'documents' => [],
        ]))->assertSessionHasErrors([
            'documents.father_death_certificate',
            'documents.martyr_certificate',
            'documents.mother_id',
        ]);
    }

    public function test_mother_national_id_cannot_be_changed_on_public_update(): void
    {
        $family = OrphanFamily::factory()->widowFamily()->create([
            'mother_national_id' => '401234567',
            'documents' => [
                'father_death_certificate' => 'orphan-documents/1/death.jpg',
                'martyr_certificate' => 'orphan-documents/1/martyr.jpg',
                'mother_id' => 'orphan-documents/1/mother.jpg',
            ],
        ]);
        $orphan = Orphan::factory()->for($family, 'family')->create();

        $this->withSession([
            OrphanCaseUpdateController::SESSION_FAMILY_ID => $family->id,
            OrphanCaseUpdateController::SESSION_VERIFIED_AT => now()->getTimestamp(),
        ])->post(route('orphan-case-update.update'), $this->updateInput($family, $orphan, [
            'mother_national_id' => '409999999',
        ]));

        $this->assertSame('401234567', $family->fresh()->mother_national_id);
    }

    public function test_bots_that_fill_the_hidden_field_on_lookup_see_not_found(): void
    {
        OrphanFamily::factory()->widowFamily()->create(['mother_national_id' => '401234567']);

        $this->post(route('orphan-case-update.lookup'), [
            'national_id' => '401234567',
            'website' => 'https://spam.example',
        ])->assertSessionHasErrors(['national_id' => __('orphan_case_update.not_found')]);

        $this->assertNull(session(OrphanCaseUpdateController::SESSION_FAMILY_ID));
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function updateInput(OrphanFamily $family, Orphan $orphan, array $overrides = []): array
    {
        return [
            'type' => $family->type,
            'mother_full_name' => $family->mother_full_name,
            'mother_national_id' => $family->mother_national_id,
            'mother_birth_date' => $family->mother_birth_date?->format('Y-m-d'),
            'mother_residence_area' => $family->mother_residence_area,
            'mother_neighborhood' => $family->mother_neighborhood,
            'mother_nearest_landmark' => $family->mother_nearest_landmark,
            'mother_birth_country' => $family->mother_birth_country,
            'mother_birth_place' => $family->mother_birth_place,
            'mother_nationality' => $family->mother_nationality,
            'mother_phone' => $family->mother_phone,
            'contact_name' => $family->contact_name,
            'contact_relation' => $family->contact_relation,
            'contact_phone' => $family->contact_phone,
            'father_full_name' => $family->father_full_name,
            'father_death_date' => $family->father_death_date?->format('Y-m-d'),
            'father_death_cause' => $family->father_death_cause,
            'orphans' => [[
                'id' => $orphan->id,
                'full_name' => $orphan->full_name,
                'birth_date' => $orphan->birth_date?->format('Y-m-d'),
                'national_id' => $orphan->national_id,
                'education_stage' => $orphan->education_stage,
                'health_status' => $orphan->health_status,
                'health_notes' => $orphan->health_notes,
            ]],
            ...$overrides,
        ];
    }
}
