<?php

namespace App\Http\Controllers;

use App\Models\Sponsorship;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * What a sponsor sees after signing in: only the people they sponsor, and only their name, age, education stage
 * and health status. Photos, ID numbers, contact details and addresses are never loaded here.
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
}
