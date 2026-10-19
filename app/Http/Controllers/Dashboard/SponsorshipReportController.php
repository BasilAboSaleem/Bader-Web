<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\OrphanFamily;
use App\Models\SponsorshipDisbursement;
use App\Support\SponsorshipFinanceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Printable case files and financial reports for staff (Bader logo only).
 */
class SponsorshipReportController extends Controller
{
    public function casePrint(OrphanFamily $orphanFamily): View
    {
        $orphanFamily->load([
            'orphans',
            'activeSponsorships.sponsor',
            'sponsorships' => fn ($query) => $query->with('sponsor')->orderByDesc('started_at'),
        ]);

        $disbursements = $this->disbursementsForFamily($orphanFamily);

        return view('reports.orphan-case', [
            'family' => $orphanFamily,
            'sponsorship' => null,
            'disbursements' => $disbursements,
            'financeTotals' => $this->totalsByCurrency($disbursements),
            'mode' => 'admin',
            'printTitle' => __('sponsors.report.case_title'),
        ]);
    }

    public function caseFinance(OrphanFamily $orphanFamily): View
    {
        $orphanFamily->load(['orphans', 'activeSponsorships.sponsor', 'sponsorships.sponsor']);

        $disbursements = $this->disbursementsForFamily($orphanFamily);

        return view('dashboard.reports.finance-case', [
            'family' => $orphanFamily,
            'sponsorName' => SponsorshipFinanceReport::primarySponsorName($orphanFamily),
            'approvedAmounts' => SponsorshipFinanceReport::approvedAmounts($orphanFamily),
            'monthRows' => SponsorshipFinanceReport::caseMonthRows($disbursements),
            'totals' => $this->totalsByCurrency($disbursements),
        ]);
    }

    public function caseFinancePrint(OrphanFamily $orphanFamily): View
    {
        $orphanFamily->load(['orphans', 'activeSponsorships.sponsor', 'sponsorships.sponsor']);

        $disbursements = $this->disbursementsForFamily($orphanFamily);

        return view('reports.finance-case', [
            'family' => $orphanFamily,
            'sponsorName' => SponsorshipFinanceReport::primarySponsorName($orphanFamily),
            'approvedAmounts' => SponsorshipFinanceReport::approvedAmounts($orphanFamily),
            'monthRows' => SponsorshipFinanceReport::caseMonthRows($disbursements),
            'totals' => $this->totalsByCurrency($disbursements),
        ]);
    }

    public function period(Request $request): View
    {
        [$year, $month] = $this->monthBounds($request);
        $disbursements = $this->disbursementsForMonth($year, $month);
        $rows = SponsorshipFinanceReport::monthSpreadsheetRows($disbursements);

        return view('dashboard.reports.finance-period', [
            'year' => $year,
            'month' => $month,
            'rows' => $rows,
            'totals' => $this->totalsByCurrency($disbursements),
        ]);
    }

    public function periodPrint(Request $request): View
    {
        [$year, $month] = $this->monthBounds($request);
        $disbursements = $this->disbursementsForMonth($year, $month);
        $rows = SponsorshipFinanceReport::monthSpreadsheetRows($disbursements);

        return view('reports.finance-period', [
            'year' => $year,
            'month' => $month,
            'rows' => $rows,
            'totals' => $this->totalsByCurrency($disbursements),
        ]);
    }

    /**
     * @return Collection<int, SponsorshipDisbursement>
     */
    private function disbursementsForFamily(OrphanFamily $family): Collection
    {
        return SponsorshipDisbursement::query()
            ->whereHas('sponsorship', fn ($query) => $query->where('orphan_family_id', $family->id))
            ->with(['sponsorship.sponsor', 'sponsorship.orphan'])
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, SponsorshipDisbursement>
     */
    private function disbursementsForMonth(int $year, int $month): Collection
    {
        return SponsorshipDisbursement::query()
            ->with([
                'sponsorship.sponsor',
                'sponsorship.orphan',
                'sponsorship.family.orphans',
            ])
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  Collection<int, SponsorshipDisbursement>  $disbursements
     * @return Collection<string, float>
     */
    private function totalsByCurrency(Collection $disbursements): Collection
    {
        return $disbursements
            ->groupBy('currency')
            ->map(fn (Collection $rows): float => (float) $rows->sum(fn (SponsorshipDisbursement $row): float => (float) $row->amount));
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function monthBounds(Request $request): array
    {
        $previous = now()->subMonth();
        $year = (int) $request->input('year', $previous->year);
        $month = (int) $request->input('month', $previous->month);

        $validated = validator(
            ['year' => $year, 'month' => $month],
            [
                'year' => ['required', 'integer', 'min:2000', 'max:2100'],
                'month' => ['required', 'integer', 'min:1', 'max:12'],
            ]
        )->validate();

        return [(int) $validated['year'], (int) $validated['month']];
    }
}
