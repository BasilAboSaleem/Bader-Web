<?php

namespace App\Support;

use App\Models\OrphanFamily;
use App\Models\Sponsorship;
use App\Models\SponsorshipDisbursement;
use Illuminate\Support\Collection;

/**
 * Shapes case and monthly finance reports for the sponsorship programme (screen + print).
 */
class SponsorshipFinanceReport
{
    /**
     * Active monthly commitment for the whole case, keyed by currency.
     *
     * @return Collection<string, float>
     */
    public static function approvedAmounts(OrphanFamily $family): Collection
    {
        $active = $family->relationLoaded('activeSponsorships')
            ? $family->activeSponsorships
            : $family->activeSponsorships()->get();

        return collect(SponsorshipAmounts::monthlyTotals($active));
    }

    /**
     * Disbursements for one case rolled up by calendar month (amount + notes only).
     *
     * @param  Collection<int, SponsorshipDisbursement>  $disbursements
     * @return Collection<int, array{period: string, amount: float, currency: string, notes: string}>
     */
    public static function caseMonthRows(Collection $disbursements): Collection
    {
        return $disbursements
            ->groupBy(fn (SponsorshipDisbursement $row): string => $row->periodLabel().'|'.$row->currency)
            ->map(function (Collection $rows): array {
                /** @var SponsorshipDisbursement $first */
                $first = $rows->first();

                return [
                    'period' => $first->periodLabel(),
                    'amount' => round((float) $rows->sum(fn (SponsorshipDisbursement $row): float => (float) $row->amount), 2),
                    'currency' => $first->currency,
                    'notes' => $rows->pluck('notes')->filter()->unique()->implode(' · ') ?: '—',
                ];
            })
            ->sortByDesc('period')
            ->values();
    }

    /**
     * One spreadsheet row per case for a given month's disbursements.
     *
     * @param  Collection<int, SponsorshipDisbursement>  $disbursements
     * @return Collection<int, array{
     *     family: OrphanFamily,
     *     case_name: string,
     *     national_id: string,
     *     phone: string,
     *     orphans_count: int,
     *     wife_amount: float,
     *     children_amount: float,
     *     displacement_amount: float|null,
     *     estimated_amount: float,
     *     started_at: ?string,
     *     month_amount: float,
     *     paid_on: ?string,
     *     sponsor_name: string,
     *     currency: string
     * }>
     */
    public static function monthSpreadsheetRows(Collection $disbursements): Collection
    {
        return $disbursements
            ->groupBy(fn (SponsorshipDisbursement $row): int => (int) $row->sponsorship->orphan_family_id)
            ->map(function (Collection $rows): array {
                /** @var OrphanFamily $family */
                $family = $rows->first()->sponsorship->family;
                $currency = (string) $rows->first()->currency;

                $wifeAmount = round((float) $rows
                    ->filter(fn (SponsorshipDisbursement $row): bool => $row->sponsorship->isForMother())
                    ->sum(fn (SponsorshipDisbursement $row): float => (float) $row->amount), 2);

                $childrenAmount = round((float) $rows
                    ->reject(fn (SponsorshipDisbursement $row): bool => $row->sponsorship->isForMother())
                    ->sum(fn (SponsorshipDisbursement $row): float => (float) $row->amount), 2);

                $monthAmount = round($wifeAmount + $childrenAmount, 2);

                $startedAt = $rows
                    ->map(fn (SponsorshipDisbursement $row) => $row->sponsorship->started_at)
                    ->filter()
                    ->sort()
                    ->first();

                $paidOn = $rows
                    ->map(fn (SponsorshipDisbursement $row) => $row->paid_on)
                    ->filter()
                    ->sort()
                    ->last();

                $sponsorName = $rows
                    ->map(fn (SponsorshipDisbursement $row): string => (string) $row->sponsorship->sponsor->name)
                    ->unique()
                    ->values()
                    ->implode(' · ');

                return [
                    'family' => $family,
                    'case_name' => $family->isWidowFamily()
                        ? (string) ($family->mother_full_name ?: $family->display_name)
                        : (string) $family->display_name,
                    'national_id' => $family->isWidowFamily()
                        ? (string) ($family->mother_national_id ?: '—')
                        : (string) ($family->orphans->pluck('national_id')->filter()->first() ?: '—'),
                    'phone' => $family->isWidowFamily()
                        ? (string) ($family->mother_phone ?: '—')
                        : (string) ($family->contact_phone ?: '—'),
                    'orphans_count' => $family->orphans->count(),
                    'wife_amount' => $wifeAmount,
                    'children_amount' => $childrenAmount,
                    'displacement_amount' => null,
                    'estimated_amount' => $monthAmount,
                    'started_at' => $startedAt?->format('Y/m/d'),
                    'month_amount' => $monthAmount,
                    'paid_on' => $paidOn?->format('Y/m/d'),
                    'sponsor_name' => $sponsorName !== '' ? $sponsorName : '—',
                    'currency' => $currency,
                ];
            })
            ->sortBy('case_name')
            ->values();
    }

    /**
     * Primary sponsor name for a case (from active sponsorships).
     */
    public static function primarySponsorName(OrphanFamily $family): string
    {
        $active = $family->relationLoaded('activeSponsorships')
            ? $family->activeSponsorships
            : $family->activeSponsorships()->with('sponsor')->get();

        if ($active->isEmpty()) {
            return '—';
        }

        return $active
            ->map(fn (Sponsorship $sponsorship): string => (string) $sponsorship->sponsor->name)
            ->unique()
            ->values()
            ->implode(' · ');
    }
}
