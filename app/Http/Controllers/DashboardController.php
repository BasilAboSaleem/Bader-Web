<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\FormSubmission;
use App\Models\ImpactMetric;
use App\Models\Program;
use App\Models\Story;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $modules = [
            [
                'key' => 'site_settings',
                'route' => 'dashboard.settings.edit',
                'stage' => 6,
                'status' => 'dashboard.status_active',
                'group' => 'settings',
            ],
            [
                'key' => 'pages',
                'route' => 'dashboard.pages.edit',
                'stage' => 6,
                'status' => 'dashboard.status_active',
                'group' => 'content',
            ],
            [
                'key' => 'programs',
                'route' => 'dashboard.programs.index',
                'stage' => 7,
                'status' => 'dashboard.status_active',
                'group' => 'content',
            ],
            [
                'key' => 'facilities',
                'route' => 'dashboard.facilities.index',
                'stage' => 7,
                'status' => 'dashboard.status_active',
                'group' => 'content',
            ],
            [
                'key' => 'campaigns',
                'route' => 'dashboard.campaigns.index',
                'stage' => 7,
                'status' => 'dashboard.status_active',
                'group' => 'content',
            ],
            [
                'key' => 'stories',
                'route' => 'dashboard.stories.index',
                'stage' => 7,
                'status' => 'dashboard.status_active',
                'group' => 'content',
            ],
            [
                'key' => 'media',
                'route' => 'dashboard.media.index',
                'stage' => 7,
                'status' => 'dashboard.status_active',
                'group' => 'content',
            ],
            [
                'key' => 'inbox',
                'route' => 'dashboard.inbox.index',
                'stage' => 8,
                'status' => 'dashboard.status_active',
                'group' => 'inquiries',
            ],
            [
                'key' => 'impact',
                'route' => 'dashboard.impact.index',
                'stage' => 8,
                'status' => 'dashboard.status_active',
                'group' => 'impact',
            ],
            [
                'key' => 'donations',
                'route' => 'dashboard.donations.index',
                'stage' => 8,
                'status' => 'dashboard.status_active',
                'group' => 'donations',
            ],
        ];

        $counts = [
            'unread_inbox' => FormSubmission::where('status', 'unread')->count(),
            'approved_metrics' => ImpactMetric::where('is_approved', true)->count(),
            'total_donations' => Donation::where('status', 'verified')->sum('amount'),
            'programs_count' => Program::count(),
            'campaigns_count' => Campaign::count(),
            'stories_count' => Story::count(),
        ];

        return view('dashboard.index', compact('modules', 'counts'));
    }
}
