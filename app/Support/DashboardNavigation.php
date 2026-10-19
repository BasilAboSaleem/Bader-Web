<?php

namespace App\Support;

use App\Models\Donation;
use App\Models\FormSubmission;
use App\Models\OrphanFamily;
use App\Models\SponsorRequest;
use App\Models\User;

class DashboardNavigation
{
    /**
     * Dashboard sections grouped by the kind of work they cover. Labels live in lang files under
     * "dashboard.nav_group.{group}" and "dashboard.module.{module}" (plus "_desc" for the description).
     *
     * @var array<string, array<string, string>>
     */
    private const GROUPS = [
        'website' => [
            'homepage' => 'dashboard.homepage.edit',
            'pages' => 'dashboard.pages.edit',
            'site_texts' => 'dashboard.site-texts.edit',
            'faqs' => 'dashboard.faqs.index',
            'impact' => 'dashboard.impact.index',
            'media' => 'dashboard.media.index',
        ],
        'projects' => [
            'programs' => 'dashboard.programs.index',
            'completed_projects' => 'dashboard.completed-projects.index',
            'campaigns' => 'dashboard.campaigns.index',
            'facilities' => 'dashboard.facilities.index',
            'regions' => 'dashboard.regions.index',
            'orphans' => 'dashboard.orphans.index',
            'sponsors' => 'dashboard.sponsors.index',
            'sponsor_requests' => 'dashboard.sponsor-requests.index',
            'disbursements' => 'dashboard.disbursements.index',
            'finance_reports' => 'dashboard.reports.finance',
            'sponsorship_cases' => 'dashboard.sponsorship-cases.index',
            'stories' => 'dashboard.stories.index',
        ],
        'operations' => [
            'inbox' => 'dashboard.inbox.index',
            'donations' => 'dashboard.donations.index',
        ],
        'settings' => [
            'site_settings' => 'dashboard.settings.edit',
            'users' => 'dashboard.users.index',
        ],
    ];

    /**
     * Board members only ever see orphan case files and monthly finance reports.
     *
     * @var array{key: string, modules: list<array{key: string, route: string, pattern: string, badge: int}>}
     */
    private const BOARD_GROUP = [
        'key' => 'projects',
        'modules' => [
            ['key' => 'orphans', 'route' => 'dashboard.orphans.index', 'pattern' => 'dashboard.orphans.*', 'badge' => 0],
            ['key' => 'finance_reports', 'route' => 'dashboard.reports.finance', 'pattern' => 'dashboard.reports.*', 'badge' => 0],
        ],
    ];

    /**
     * Sponsors only ever see their own portal.
     *
     * @var array{key: string, modules: list<array{key: string, route: string, pattern: string, badge: int}>}
     */
    private const SPONSOR_GROUP = [
        'key' => 'sponsor_portal',
        'modules' => [
            ['key' => 'my_sponsorships', 'route' => 'sponsor.home', 'pattern' => 'sponsor.*', 'badge' => 0],
        ],
    ];

    /**
     * Sections the given user may open, grouped, with a count of items waiting for action.
     *
     * @return list<array{key: string, modules: list<array{key: string, route: string, pattern: string, badge: int}>}>
     */
    public static function groups(?User $user): array
    {
        if (! $user?->isStaff()) {
            return $user?->isSponsor() ? [self::SPONSOR_GROUP] : [];
        }

        if ($user->isBoardMember()) {
            $badges = self::badges();
            $group = self::BOARD_GROUP;
            $group['modules'] = array_map(function (array $module) use ($badges): array {
                $module['badge'] = $badges[$module['key']] ?? 0;

                return $module;
            }, $group['modules']);

            return [$group];
        }

        $badges = self::badges();
        $groups = [];

        foreach (self::GROUPS as $group => $modules) {
            $items = [];

            foreach ($modules as $module => $route) {
                if ($module === 'users' && ! $user->isSuperAdmin()) {
                    continue;
                }

                if ($module === 'orphans' && ! $user->canViewOrphans()) {
                    continue;
                }

                if (in_array($module, ['sponsors', 'sponsor_requests', 'disbursements'], true) && ! $user->canManageFinance()) {
                    continue;
                }

                if ($module === 'finance_reports' && ! $user->canViewFinanceReports()) {
                    continue;
                }

                $items[] = [
                    'key' => $module,
                    'route' => $route,
                    'pattern' => preg_replace('/\.[^.]+$/', '.*', $route),
                    'badge' => $badges[$module] ?? 0,
                ];
            }

            if ($items !== []) {
                $groups[] = ['key' => $group, 'modules' => $items];
            }
        }

        return $groups;
    }

    /**
     * Items waiting for the team: unread messages, donations not yet confirmed, orphan registrations and sponsor requests.
     *
     * @return array{inbox: int, donations: int, orphans: int, sponsor_requests: int}
     */
    public static function badges(): array
    {
        return once(fn (): array => [
            'inbox' => FormSubmission::where('status', 'unread')->count(),
            'donations' => Donation::where('status', 'pending')->count(),
            'orphans' => OrphanFamily::pending()->count(),
            'sponsor_requests' => SponsorRequest::pending()->count(),
        ]);
    }
}
