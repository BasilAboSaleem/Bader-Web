<?php

namespace Database\Factories;

use App\Models\SponsorRequest;
use App\Models\Sponsorship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SponsorRequest>
 */
class SponsorRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sponsorship_id' => Sponsorship::factory(),
            'sponsor_id' => fn (array $attributes): int => Sponsorship::findOrFail($attributes['sponsorship_id'])->sponsor_id,
            'type' => SponsorRequest::TYPE_INCREASE,
            'requested_amount' => 100,
            'currency' => 'USD',
            'message' => 'أرغب بزيادة مبلغ الكفالة.',
            'status' => SponsorRequest::STATUS_PENDING,
        ];
    }

    public function stop(): static
    {
        return $this->state(fn (): array => [
            'type' => SponsorRequest::TYPE_STOP,
            'requested_amount' => null,
            'currency' => null,
            'message' => 'أرغب بوقف الكفالة.',
        ]);
    }
}
