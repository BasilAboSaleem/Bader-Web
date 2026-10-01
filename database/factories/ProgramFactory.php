<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(2, true);

        return [
            'key' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 99999),
            'title_ar' => 'برنامج '.$title,
            'title_en' => Str::title($title).' Program',
            'description_ar' => 'وصف البرنامج '.$title,
            'description_en' => 'Program description for '.$title,
            'is_flagship' => false,
            'order' => 0,
            'status' => 'published',
        ];
    }
}
