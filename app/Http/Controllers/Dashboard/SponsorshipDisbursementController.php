<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sponsorship;
use App\Models\SponsorshipDisbursement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Monthly payout ledger: finance confirms which active sponsorships were paid for a given month,
 * then sponsors see those months as disbursed on their case pages and reports.
 */
class SponsorshipDisbursementController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $disbursements = SponsorshipDisbursement::query()
            ->with(['sponsorship.sponsor', 'sponsorship.family', 'sponsorship.orphan', 'creator'])
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->orderBy('id')
            ->get();

        return view('dashboard.disbursements.index', [
            'year' => $year,
            'month' => $month,
            'disbursements' => $disbursements,
            'totals' => $disbursements->groupBy('currency')->map->sum(fn (SponsorshipDisbursement $row): float => (float) $row->amount),
        ]);
    }

    public function create(Request $request): View
    {
        $previous = now()->subMonth();
        $year = (int) $request->input('year', $previous->year);
        $month = (int) $request->input('month', $previous->month);

        $active = Sponsorship::query()
            ->active()
            ->with(['sponsor', 'family', 'orphan'])
            ->orderBy('sponsor_id')
            ->orderBy('orphan_family_id')
            ->get();

        $existing = SponsorshipDisbursement::query()
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->get()
            ->keyBy('sponsorship_id');

        return view('dashboard.disbursements.create', [
            'year' => $year,
            'month' => $month,
            'sponsorships' => $active,
            'existing' => $existing,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'period_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
            'paid_on' => ['required', 'date'],
            'rows' => ['nullable', 'array'],
            'rows.*.sponsorship_id' => ['nullable', 'integer', Rule::exists('sponsorships', 'id')],
            'rows.*.amount' => ['nullable', 'numeric', 'min:0.01', 'max:100000'],
            'rows.*.currency' => ['nullable', Rule::in(config('bader.sponsorship_currencies'))],
            'rows.*.usd_rate' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'rows.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $rows = collect($validated['rows'] ?? [])
            ->filter(fn (array $row): bool => filled($row['sponsorship_id'] ?? null))
            ->values();

        if ($rows->isEmpty()) {
            throw ValidationException::withMessages(['rows' => __('sponsors.disbursements.none_selected')]);
        }

        foreach ($rows as $index => $row) {
            if (! filled($row['amount'] ?? null) || ! filled($row['currency'] ?? null)) {
                throw ValidationException::withMessages(["rows.{$index}.amount" => __('sponsors.disbursements.invalid_rows')]);
            }
        }

        $ids = $rows->pluck('sponsorship_id')->map(fn ($id): int => (int) $id);
        $sponsorships = Sponsorship::query()->whereIn('id', $ids)->get()->keyBy('id');

        if ($sponsorships->count() !== $ids->unique()->count()) {
            throw ValidationException::withMessages(['rows' => __('sponsors.disbursements.invalid_rows')]);
        }

        DB::transaction(function () use ($validated, $rows, $sponsorships, $request): void {
            foreach ($rows as $row) {
                $sponsorship = $sponsorships->get((int) $row['sponsorship_id']);

                SponsorshipDisbursement::query()->updateOrCreate(
                    [
                        'sponsorship_id' => $sponsorship->id,
                        'period_year' => (int) $validated['period_year'],
                        'period_month' => (int) $validated['period_month'],
                    ],
                    [
                        'amount' => $row['amount'],
                        'currency' => $row['currency'],
                        'usd_rate' => $row['usd_rate'] ?? null,
                        'paid_on' => $validated['paid_on'],
                        'notes' => $row['notes'] ?? null,
                        'created_by' => $request->user()->id,
                    ]
                );
            }
        });

        return redirect()->route('dashboard.disbursements.index', [
            'year' => $validated['period_year'],
            'month' => $validated['period_month'],
        ])->with('status', __('sponsors.disbursements.saved'));
    }

    public function destroy(SponsorshipDisbursement $disbursement): RedirectResponse
    {
        $year = $disbursement->period_year;
        $month = $disbursement->period_month;
        $disbursement->delete();

        return redirect()->route('dashboard.disbursements.index', ['year' => $year, 'month' => $month])
            ->with('status', __('sponsors.disbursements.deleted'));
    }
}
