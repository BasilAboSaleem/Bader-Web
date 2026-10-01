<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'key' => Str::slug($name).'-'.fake()->unique()->numberBetween(10, 9999),
            'name_ar' => 'مرفق '.$name,
            'name_en' => Str::title($name).' facility',
            'description_ar' => 'وصف مرفق '.$name,
            'description_en' => 'About the '.$name.' facility',
            'location_ar' => 'غزة',
            'location_en' => 'Gaza',
            'established_year' => 2024,
            'order' => 0,
            'status' => 'published',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['status' => 'draft']);
    }
}
