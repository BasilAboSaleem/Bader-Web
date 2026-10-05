<?php

namespace Database\Factories;

use App\Models\CompletedProject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CompletedProject>
 */
class CompletedProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'key' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 99999),
            'title_ar' => 'مشروع منفذ '.$title,
            'title_en' => Str::title($title),
            'description_ar' => 'وصف المشروع المنفذ '.$title,
            'description_en' => 'Completed project description for '.$title,
            'completed_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'beneficiaries' => fake()->numberBetween(50, 5000),
            'cost' => fake()->numberBetween(1000, 50000),
            'status' => 'published',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['status' => 'draft']);
    }
}
