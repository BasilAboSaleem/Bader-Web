<?php

namespace Database\Factories;

use App\Models\SponsorshipCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SponsorshipCase>
 */
class SponsorshipCaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->firstName();

        return [
            'code' => 'GZ-'.fake()->unique()->numberBetween(100, 99999),
            'type' => 'orphan',
            'name_ar' => 'الطفل '.$name,
            'name_en' => $name,
            'age' => fake()->numberBetween(4, 16),
            'gender' => fake()->randomElement(['male', 'female']),
            'monthly_amount' => 40,
            'duration_months' => 12,
            'bio_ar' => 'نبذة عن '.$name,
            'bio_en' => 'About '.$name,
            'status' => SponsorshipCase::STATUS_AVAILABLE,
            'waiting_since' => now()->subMonths(fake()->numberBetween(1, 24))->toDateString(),
        ];
    }

    public function sponsored(): static
    {
        return $this->state(fn (): array => ['status' => SponsorshipCase::STATUS_SPONSORED]);
    }

    public function hidden(): static
    {
        return $this->state(fn (): array => ['status' => SponsorshipCase::STATUS_HIDDEN]);
    }
}
