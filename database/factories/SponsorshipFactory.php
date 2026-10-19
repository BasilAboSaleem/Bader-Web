<?php

namespace Database\Factories;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sponsorship>
 */
class SponsorshipFactory extends Factory
{
    /**
     * An active sponsorship of one orphan.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sponsor_id' => Sponsor::factory(),
            'orphan_id' => Orphan::factory(),
            'orphan_family_id' => fn (array $attributes): int => Orphan::findOrFail($attributes['orphan_id'])->orphan_family_id,
            'beneficiary_type' => Sponsorship::BENEFICIARY_ORPHAN,
            'monthly_amount' => 50,
            'currency' => 'USD',
            'started_at' => now()->startOfMonth()->toDateString(),
            'renewal_type' => Sponsorship::RENEWAL_RECURRING,
            'planned_ended_at' => null,
        ];
    }

    public function oneTime(?string $endsAt = null): static
    {
        return $this->state(fn (): array => [
            'renewal_type' => Sponsorship::RENEWAL_ONE_TIME,
            'planned_ended_at' => $endsAt ?? now()->addMonths(6)->toDateString(),
        ]);
    }

    /**
     * The mother of a widow family.
     */
    public function forMother(?OrphanFamily $family = null): static
    {
        return $this->state(fn (): array => [
            'orphan_id' => null,
            'orphan_family_id' => $family?->id ?? OrphanFamily::factory()->approved()->widowFamily(),
            'beneficiary_type' => Sponsorship::BENEFICIARY_MOTHER,
            'monthly_amount' => 150,
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn (): array => ['ended_at' => now()->subDay()->toDateString()]);
    }
}
