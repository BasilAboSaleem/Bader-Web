<?php

namespace Database\Factories;

use App\Models\Sponsorship;
use App\Models\SponsorshipDisbursement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SponsorshipDisbursement>
 */
class SponsorshipDisbursementFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $month = now()->subMonth()->startOfMonth();

        return [
            'sponsorship_id' => Sponsorship::factory(),
            'period_year' => (int) $month->year,
            'period_month' => (int) $month->month,
            'amount' => 50,
            'currency' => 'USD',
            'usd_rate' => 1,
            'paid_on' => $month->toDateString(),
        ];
    }
}
