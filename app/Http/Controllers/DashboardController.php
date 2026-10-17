<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Support\DashboardNavigation;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        if ($request->user()->isSponsor()) {
            return redirect()->route('sponsor.home');
        }

        abort_unless($request->user()->isStaff(), 403);

        $badges = DashboardNavigation::badges();

        $stats = [
            ['key' => 'unread_inbox', 'value' => (string) $badges['inbox'], 'route' => 'dashboard.inbox.index', 'icon' => 'inbox', 'alert' => $badges['inbox'] > 0],
            ['key' => 'pending_donations', 'value' => (string) $badges['donations'], 'route' => 'dashboard.donations.index', 'icon' => 'donations', 'alert' => $badges['donations'] > 0],
            ['key' => 'verified_total', 'value' => Money::format(Donation::where('status', 'verified')->sum('amount')), 'route' => 'dashboard.donations.index', 'icon' => 'impact', 'alert' => false],
            ['key' => 'published_campaigns', 'value' => (string) Campaign::published()->count(), 'route' => 'dashboard.campaigns.index', 'icon' => 'campaigns', 'alert' => false],
        ];

        return view('dashboard.index', [
            'groups' => DashboardNavigation::groups($request->user()),
            'stats' => $stats,
        ]);
    }
}
