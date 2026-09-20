<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'modules' => [
                [
                    'key' => 'site_settings',
                    'stage' => 6,
                    'status' => 'dashboard.status_soon',
                    'group' => 'settings',
                ],
                [
                    'key' => 'pages',
                    'stage' => 6,
                    'status' => 'dashboard.status_soon',
                    'group' => 'content',
                ],
                [
                    'key' => 'programs',
                    'stage' => 7,
                    'status' => 'dashboard.status_soon',
                    'group' => 'content',
                ],
                [
                    'key' => 'campaigns',
                    'stage' => 7,
                    'status' => 'dashboard.status_soon',
                    'group' => 'content',
                ],
                [
                    'key' => 'stories',
                    'stage' => 7,
                    'status' => 'dashboard.status_soon',
                    'group' => 'content',
                ],
                [
                    'key' => 'inbox',
                    'stage' => 8,
                    'status' => 'dashboard.status_soon',
                    'group' => 'inquiries',
                ],
            ],
            'quickStats' => [
                [
                    'label' => 'dashboard.quick_info.public_status',
                    'value' => 'dashboard.quick_info.public_status_val',
                    'badge' => 'dashboard.status_active',
                ],
                [
                    'label' => 'dashboard.quick_info.default_lang',
                    'value' => 'dashboard.quick_info.default_lang_val',
                    'badge' => 'ar · en',
                ],
                [
                    'label' => 'dashboard.quick_info.operational_hqs',
                    'value' => 'dashboard.quick_info.operational_hqs_val',
                    'badge' => 'brand.name',
                ],
            ],
        ]);
    }
}
