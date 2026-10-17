<?php

namespace Database\Factories;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Orphan>
 */
class OrphanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'orphan_family_id' => OrphanFamily::factory(),
            'full_name' => fake()->randomElement(['أحمد', 'يوسف', 'مريم', 'سلمى', 'عمر']).' محمد سالم أبو عيد',
            'birth_date' => now()->subYears(fake()->numberBetween(3, 15))->toDateString(),
            'national_id' => fake()->numerify('4########'),
            'education_stage' => 'primary',
            'health_status' => 'healthy',
            'sponsorship_status' => OrphanFamily::SPONSORSHIP_AVAILABLE,
        ];
    }

    public function sponsored(): static
    {
        return $this->state(fn (): array => ['sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED]);
    }
}
