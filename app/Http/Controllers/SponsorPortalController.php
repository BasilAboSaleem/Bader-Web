<?php

namespace App\Http\Controllers;

use App\Models\SponsorRequest;
use App\Models\Sponsorship;
use App\Models\SponsorshipDisbursement;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * What a sponsor sees after signing in: a private list of their cases, a full case file per sponsorship,
 * requests to stop or change amounts, printable case reports, and financial disbursement history.
 */
class SponsorPortalController extends Controller
{
    public function index(Request $request): View
    {
        $sponsor = $request->user()->sponsor;

        $sponsorships = $sponsor === null ? collect() : $sponsor->sponsorships()
            ->with([
                'family' => fn (BelongsTo $family) => $family->select(['id', 'type', 'mother_full_name', 'mother_birth_date']),
                'orphan' => fn (BelongsTo $orphan) => $orphan->select(['id', 'full_name', 'birth_date', 'education_stage', 'health_status']),
            ])
            ->orderByRaw('ended_at is not null')
            ->orderByDesc('started_at')
            ->get();

        return view('sponsor.index', [
            'sponsor' => $sponsor,
            'activeSponsorships' => $sponsorships->filter(fn (Sponsorship $sponsorship): bool => $sponsorship->isActive())->values(),
            'endedSponsorships' => $sponsorships->reject(fn (Sponsorship $sponsorship): bool => $sponsorship->isActive())->values(),
        ]);
    }

    public function show(Request $request, Sponsorship $sponsorship): View
    {
        $this->authorizeSponsorship($request, $sponsorship);

        $sponsorship->load([
            'family.orphans',
            'orphan',
            'requests' => fn ($query) => $query->latest(),
            'disbursements' => fn ($query) => $query->orderByDesc('period_year')->orderByDesc('period_month'),
        ]);

        return view('sponsor.show', [
            'sponsor' => $request->user()->sponsor,
            'sponsorship' => $sponsorship,
            'family' => $sponsorship->family,
            'pendingRequest' => $sponsorship->requests->firstWhere('status', SponsorRequest::STATUS_PENDING),
        ]);
    }

    public function storeRequest(Request $request, Sponsorship $sponsorship): RedirectResponse
    {
        $this->authorizeSponsorship($request, $sponsorship);
        abort_unless($sponsorship->isActive(), 404);

        if ($sponsorship->requests()->pending()->exists()) {
            throw ValidationException::withMessages(['type' => __('sponsors.requests.already_pending')]);
        }

        $validated = $request->validate([
            'type' => ['required', Rule::in(SponsorRequest::TYPES)],
            'requested_amount' => [
                'nullable',
                'required_unless:type,'.SponsorRequest::TYPE_STOP,
                'numeric',
                'min:1',
                'max:100000',
            ],
            'message' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'type' => __('sponsors.requests.type'),
            'requested_amount' => __('sponsors.field.monthly_amount'),
            'message' => __('sponsors.requests.message'),
        ]);

        if ($validated['type'] !== SponsorRequest::TYPE_STOP) {
            $current = (float) $sponsorship->monthly_amount;
            $requested = (float) $validated['requested_amount'];

            if ($validated['type'] === SponsorRequest::TYPE_INCREASE && $requested <= $current) {
                throw ValidationException::withMessages(['requested_amount' => __('sponsors.requests.increase_must_be_higher')]);
            }

            if ($validated['type'] === SponsorRequest::TYPE_DECREASE && $requested >= $current) {
                throw ValidationException::withMessages(['requested_amount' => __('sponsors.requests.decrease_must_be_lower')]);
            }
        }

        SponsorRequest::create([
            'sponsorship_id' => $sponsorship->id,
            'sponsor_id' => $sponsorship->sponsor_id,
            'type' => $validated['type'],
            'requested_amount' => $validated['type'] === SponsorRequest::TYPE_STOP ? null : $validated['requested_amount'],
            'currency' => $validated['type'] === SponsorRequest::TYPE_STOP ? null : $sponsorship->currency,
            'message' => $validated['message'] ?? null,
            'status' => SponsorRequest::STATUS_PENDING,
        ]);

        return redirect()->route('sponsor.show', $sponsorship)
            ->with('status', __('sponsors.requests.submitted'));
    }

    public function print(Request $request, Sponsorship $sponsorship): View
    {
        $this->authorizeSponsorship($request, $sponsorship);

        $sponsorship->load(['family.orphans', 'orphan', 'sponsor', 'disbursements' => fn ($query) => $query->orderByDesc('period_year')->orderByDesc('period_month')]);

        $disbursements = $sponsorship->disbursements->each(
            fn (SponsorshipDisbursement $row) => $row->setRelation('sponsorship', $sponsorship)
        );

        return view('reports.orphan-case', [
            'family' => $sponsorship->family,
            'sponsorship' => $sponsorship,
            'disbursements' => $disbursements,
            'financeTotals' => $disbursements->groupBy('currency')->map->sum(fn (SponsorshipDisbursement $row): float => (float) $row->amount),
            'mode' => 'sponsor',
            'printTitle' => __('sponsors.report.case_title'),
        ]);
    }

    public function finance(Request $request, Sponsorship $sponsorship): View
    {
        $this->authorizeSponsorship($request, $sponsorship);

        $sponsorship->load(['family', 'orphan']);

        $disbursements = $sponsorship->disbursements()
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->get();

        return view('sponsor.finance', [
            'sponsor' => $request->user()->sponsor,
            'sponsorship' => $sponsorship,
            'disbursements' => $disbursements,
            'totals' => $disbursements->groupBy('currency')->map->sum(fn (SponsorshipDisbursement $row): float => (float) $row->amount),
        ]);
    }

    public function financePrint(Request $request, Sponsorship $sponsorship): View
    {
        $this->authorizeSponsorship($request, $sponsorship);

        $sponsorship->load(['family', 'orphan', 'sponsor']);

        $disbursements = $sponsorship->disbursements()
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->get();

        return view('reports.finance-case', [
            'sponsorship' => $sponsorship,
            'family' => $sponsorship->family,
            'disbursements' => $disbursements,
            'totals' => $disbursements->groupBy('currency')->map->sum(fn (SponsorshipDisbursement $row): float => (float) $row->amount),
            'mode' => 'sponsor',
        ]);
    }

    private function authorizeSponsorship(Request $request, Sponsorship $sponsorship): void
    {
        $sponsor = $request->user()->sponsor;

        abort_if($sponsor === null || (int) $sponsorship->sponsor_id !== (int) $sponsor->id, 404);
    }
}
