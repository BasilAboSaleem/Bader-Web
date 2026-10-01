<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Region>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'key' => Str::slug($name).'-'.fake()->unique()->numberBetween(10, 9999),
            'name_ar' => 'منطقة '.$name,
            'name_en' => $name,
            'description_ar' => 'وصف منطقة '.$name,
            'description_en' => 'Description of '.$name,
            'map_x' => fake()->numberBetween(20, 80),
            'map_y' => fake()->numberBetween(10, 90),
            'order' => 0,
            'status' => 'published',
        ];
    }
}
