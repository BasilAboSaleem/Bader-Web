<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function about(): View
    {
        return $this->page('about', [
            'about.mission',
            'about.work',
            'about.independence',
        ]);
    }

    public function programs(): View
    {
        return view('pages.programs', [
            'programs' => [
                'water', 'food', 'shelter', 'winter', 'health', 'education',
                'social_protection', 'economic_empowerment', 'community_partnerships', 'zakat', 'sacrifices',
            ],
        ]);
    }

    public function campaigns(): View
    {
        return view('pages.campaigns', [
            'campaigns' => [
                ['key' => 'water', 'goal' => '120,000', 'currency' => 'campaign.currency'],
                ['key' => 'education', 'goal' => null, 'currency' => null],
            ],
        ]);
    }

    public function impact(): View
    {
        return $this->page('impact', [
            'impact.verification',
            'impact.operations',
            'impact.reporting',
        ]);
    }

    public function news(): View
    {
        return view('pages.news', [
            'stories' => [
                ['key' => 'quran_honor', 'date' => '2025-04-29'],
                ['key' => 'deir_balah', 'date' => '2025-04-29'],
                ['key' => 'quran_camp', 'date' => '2025-04-29'],
            ],
        ]);
    }

    public function sponsorship(): View
    {
        return $this->page('sponsorship', [
            'sponsorship.orphans',
            'sponsorship.widows',
            'sponsorship.dignity',
        ]);
    }

    public function donate(): View
    {
        return view('pages.donate', [
            'campaignKey' => 'water',
        ]);
    }

    public function partners(): View
    {
        return $this->page('partners', [
            'partners.local',
            'partners.international',
            'partners.community',
        ]);
    }

    public function volunteer(): View
    {
        return $this->page('volunteer', [
            'volunteer.field',
            'volunteer.skills',
            'volunteer.commitment',
        ]);
    }

    public function faq(): View
    {
        return view('pages.faq', [
            'questions' => ['identity', 'politics', 'location', 'work', 'long_term'],
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    /**
     * @param  list<string>  $sections
     */
    private function page(string $key, array $sections): View
    {
        return view('pages.public', compact('key', 'sections'));
    }
}
