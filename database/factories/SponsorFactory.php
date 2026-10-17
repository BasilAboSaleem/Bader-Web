<?php

namespace Database\Factories;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sponsor>
 */
class SponsorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => null,
            'phone' => fake()->numerify('+9665########'),
            'country' => 'السعودية',
            'preferred_currency' => 'USD',
        ];
    }

    /**
     * A sponsor who can sign in to follow their sponsorships.
     */
    public function withAccount(): static
    {
        return $this->state(fn (): array => ['email' => fake()->unique()->safeEmail()])
            ->afterCreating(function (Sponsor $sponsor): void {
                $user = User::factory()->create([
                    'name' => $sponsor->name,
                    'email' => $sponsor->email,
                    'role' => User::ROLE_SPONSOR,
                ]);

                $sponsor->forceFill(['user_id' => $user->id])->save();
            });
    }
}
