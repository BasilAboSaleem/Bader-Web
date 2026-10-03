<?php

namespace App\Support;

use App\Models\Donation;
use App\Models\FormSubmission;
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
            'campaigns' => 'dashboard.campaigns.index',
            'facilities' => 'dashboard.facilities.index',
            'regions' => 'dashboard.regions.index',
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
     * Sections the given user may open, grouped, with a count of items waiting for action.
     *
     * @return list<array{key: string, modules: list<array{key: string, route: string, pattern: string, badge: int}>}>
     */
    public static function groups(?User $user): array
    {
        $badges = self::badges();
        $groups = [];

        foreach (self::GROUPS as $group => $modules) {
            $items = [];

            foreach ($modules as $module => $route) {
                if ($module === 'users' && ! $user?->isSuperAdmin()) {
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
     * Items waiting for the team: unread messages and donations not yet confirmed.
     *
     * @return array{inbox: int, donations: int}
     */
    public static function badges(): array
    {
        return once(fn (): array => [
            'inbox' => FormSubmission::where('status', 'unread')->count(),
            'donations' => Donation::where('status', 'pending')->count(),
        ]);
    }
}
