<?php

namespace App\Support;

use App\Models\Sponsorship;
use Illuminate\Support\Collection;

/**
 * Amounts in the sponsorship programme always carry their currency, and totals are kept per currency
 * so shekels, dollars and riyals are never added together.
 */
class SponsorshipAmounts
{
    private const SYMBOLS = ['USD' => '$', 'ILS' => '₪', 'SAR' => 'SAR '];

    /**
     * "$1,250.00", "₪600.00" or "SAR 300.00", always with Western digits.
     */
    public static function format(int|float|string|null $amount, string $currency): string
    {
        return (self::SYMBOLS[$currency] ?? $currency.' ').number_format((float) $amount, 2);
    }

    /**
     * Monthly amounts added up per currency, in the configured currency order.
     *
     * @param  Collection<int, Sponsorship>  $sponsorships
     * @return array<string, float>
     */
    public static function monthlyTotals(Collection $sponsorships): array
    {
        $totals = [];

        foreach (config('bader.sponsorship_currencies') as $currency) {
            $inCurrency = $sponsorships->where('currency', $currency);

            if ($inCurrency->isNotEmpty()) {
                $totals[$currency] = round($inCurrency->sum(fn (Sponsorship $sponsorship): float => (float) $sponsorship->monthly_amount), 2);
            }
        }

        return $totals;
    }
}
