<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'facilities' => ['water_plant', 'bakery', 'school'],
            'programs' => [
                'water',
                'food',
                'shelter',
                'winter',
                'health',
                'education',
                'social_protection',
                'economic_empowerment',
                'community_partnerships',
                'zakat',
                'sacrifices',
            ],
            'campaign' => [
                'key' => 'water',
                'goal' => 120000,
                'currency' => 'campaign.currency',
            ],
            'impacts' => ['families', 'water', 'bread', 'students'],
            'stories' => [
                [
                    'key' => 'quran_honor',
                    'date' => '2025-04-29',
                ],
                [
                    'key' => 'deir_balah',
                    'date' => '2025-04-29',
                ],
                [
                    'key' => 'quran_camp',
                    'date' => '2025-04-29',
                ],
            ],
        ]);
    }
}
