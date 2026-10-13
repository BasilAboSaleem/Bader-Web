<?php

namespace Database\Factories;

use App\Models\OrphanFamily;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrphanFamily>
 */
class OrphanFamilyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => OrphanFamily::TYPE_ORPHAN,
            'source' => OrphanFamily::SOURCE_WEBSITE,
            'review_status' => OrphanFamily::REVIEW_PENDING,
            'sponsorship_status' => OrphanFamily::SPONSORSHIP_AVAILABLE,
            'contact_name' => 'عبد الله سالم أبو عيد',
            'contact_relation' => 'العم',
            'contact_phone' => '0599'.fake()->numerify('######'),
            'father_full_name' => 'محمد سالم خليل أبو عيد',
            'father_death_date' => now()->subYears(2)->toDateString(),
            'father_death_cause' => 'استشهد في قصف',
            'documents' => [
                'father_death_certificate' => 'orphan-documents/test/death.jpg',
                'martyr_certificate' => 'orphan-documents/test/martyr.jpg',
                'mother_id' => 'orphan-documents/test/mother-id.jpg',
            ],
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => ['review_status' => OrphanFamily::REVIEW_APPROVED, 'reviewed_at' => now()]);
    }

    public function rejected(): static
    {
        return $this->state(fn (): array => ['review_status' => OrphanFamily::REVIEW_REJECTED, 'reviewed_at' => now()]);
    }

    public function sponsored(): static
    {
        return $this->state(fn (): array => [
            'sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED,
            'sponsor_name' => fake()->name(),
            'sponsorship_monthly_amount' => 150,
            'sponsorship_started_at' => now()->startOfMonth(),
        ]);
    }

    public function widowFamily(): static
    {
        return $this->state(fn (): array => [
            'type' => OrphanFamily::TYPE_WIDOW_FAMILY,
            'contact_name' => null,
            'contact_relation' => null,
            'contact_phone' => null,
            'mother_full_name' => 'فاطمة أحمد حسن النجار',
            'mother_national_id' => fake()->numerify('4########'),
            'mother_birth_date' => '1990-04-12',
            'mother_residence_area' => 'nuseirat_camp',
            'mother_neighborhood' => 'بلوك 5',
            'mother_nearest_landmark' => 'مسجد القسام',
            'mother_birth_country' => 'فلسطين',
            'mother_birth_place' => 'غزة',
            'mother_nationality' => 'فلسطينية',
            'mother_phone' => '0597'.fake()->numerify('######'),
        ]);
    }
}
