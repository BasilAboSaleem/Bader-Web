<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $topic = fake()->unique()->words(3, true);

        return [
            'question_ar' => 'سؤال عن '.$topic.'؟',
            'question_en' => 'A question about '.$topic.'?',
            'answer_ar' => 'إجابة عن '.$topic.'.',
            'answer_en' => 'An answer about '.$topic.'.',
            'order' => 0,
            'status' => 'published',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'draft',
        ]);
    }
}
