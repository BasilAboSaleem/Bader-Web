<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\SponsorshipCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SponsorshipPageController extends Controller
{
    public function index(Request $request): View
    {
        $regions = Region::published()->get();

        $selectedType = in_array($request->query('type'), SponsorshipCase::TYPES, true) ? $request->query('type') : null;
        $selectedRegion = $regions->firstWhere('key', $request->query('region'));

        $cases = SponsorshipCase::available()
            ->longestWaiting()
            ->with('region')
            ->when($selectedType, fn (Builder $query) => $query->where('type', $selectedType))
            ->when($selectedRegion, fn (Builder $query) => $query->where('region_id', $selectedRegion->id))
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'waiting' => SponsorshipCase::available()->count(),
            'sponsored' => SponsorshipCase::where('status', SponsorshipCase::STATUS_SPONSORED)->count(),
            'from' => SponsorshipCase::available()->min('monthly_amount'),
        ];

        return view('pages.sponsorship', compact('cases', 'regions', 'selectedType', 'selectedRegion', 'stats'));
    }

    public function show(string $code): View
    {
        $case = SponsorshipCase::visible()
            ->where('code', $code)
            ->with('region')
            ->firstOrFail();

        $otherCases = SponsorshipCase::available()
            ->whereKeyNot($case->getKey())
            ->orderByRaw('type = ? desc', [$case->type])
            ->longestWaiting()
            ->with('region')
            ->take(3)
            ->get();

        return view('pages.sponsorship-show', compact('case', 'otherCases'));
    }
}
