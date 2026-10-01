<?php

namespace Database\Factories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
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
            'title_ar' => 'مشروع '.$title,
            'title_en' => Str::title($title),
            'description_ar' => 'وصف المشروع '.$title,
            'description_en' => 'Project description for '.$title,
            'goal_amount' => 10000,
            'raised_amount' => 2500,
            'currency_ar' => 'دولار أمريكي',
            'currency_en' => 'USD',
            'is_featured' => false,
            'status' => 'published',
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => true]);
    }

    public function ongoing(): static
    {
        return $this->state(fn (): array => ['goal_amount' => null, 'raised_amount' => 0]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['status' => 'draft']);
    }
}
