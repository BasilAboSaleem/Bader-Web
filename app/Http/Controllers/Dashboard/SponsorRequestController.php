<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SponsorRequest;
use App\Support\SponsorshipManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Review stop / amount-change requests submitted by sponsors from their portal.
 */
class SponsorRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString() ?: SponsorRequest::STATUS_PENDING;

        if (! in_array($status, SponsorRequest::STATUSES, true)) {
            $status = SponsorRequest::STATUS_PENDING;
        }

        $requests = SponsorRequest::query()
            ->with(['sponsor', 'sponsorship.family', 'sponsorship.orphan', 'reviewer'])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('dashboard.sponsor-requests.index', [
            'requests' => $requests,
            'status' => $status,
            'statusCounts' => [
                SponsorRequest::STATUS_PENDING => SponsorRequest::query()->pending()->count(),
                SponsorRequest::STATUS_APPROVED => SponsorRequest::query()->where('status', SponsorRequest::STATUS_APPROVED)->count(),
                SponsorRequest::STATUS_REJECTED => SponsorRequest::query()->where('status', SponsorRequest::STATUS_REJECTED)->count(),
            ],
        ]);
    }

    public function update(Request $request, SponsorRequest $sponsorRequest): RedirectResponse
    {
        abort_unless($sponsorRequest->isPending(), 404);

        $validated = $request->validate([
            'decision' => ['required', Rule::in([SponsorRequest::STATUS_APPROVED, SponsorRequest::STATUS_REJECTED])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validated['decision'] === SponsorRequest::STATUS_APPROVED) {
            SponsorshipManager::applySponsorRequest($sponsorRequest, $request->user());
        }

        $sponsorRequest->update([
            'status' => $validated['decision'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return redirect()->route('dashboard.sponsor-requests.index')
            ->with('status', __('sponsors.requests.'.$validated['decision']));
    }
}
