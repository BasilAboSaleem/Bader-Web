<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Support\DashboardNavigation;
use App\Support\Money;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $badges = DashboardNavigation::badges();

        $stats = [
            ['key' => 'unread_inbox', 'value' => (string) $badges['inbox'], 'route' => 'dashboard.inbox.index', 'icon' => 'inbox', 'alert' => $badges['inbox'] > 0],
            ['key' => 'pending_donations', 'value' => (string) $badges['donations'], 'route' => 'dashboard.donations.index', 'icon' => 'donations', 'alert' => $badges['donations'] > 0],
            ['key' => 'verified_total', 'value' => Money::format(Donation::where('status', 'verified')->sum('amount')), 'route' => 'dashboard.donations.index', 'icon' => 'impact', 'alert' => false],
            ['key' => 'published_campaigns', 'value' => (string) Campaign::published()->count(), 'route' => 'dashboard.campaigns.index', 'icon' => 'campaigns', 'alert' => false],
        ];

        return view('dashboard.index', [
            'groups' => DashboardNavigation::groups(auth()->user()),
            'stats' => $stats,
        ]);
    }
}
