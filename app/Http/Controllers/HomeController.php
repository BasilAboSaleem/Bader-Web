<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\ImpactMetric;
use App\Models\Program;
use App\Models\Region;
use App\Models\SponsorshipCase;
use App\Models\Story;
use App\Support\SiteSettings;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $publishedCampaigns = fn ($campaigns) => $campaigns->published();

        $heroCampaigns = Campaign::featured()->ordered()->with(['program', 'region'])->take(5)->get();

        if ($heroCampaigns->isEmpty()) {
            $heroCampaigns = Campaign::published()->ordered()->with(['program', 'region'])->take(3)->get();
        }

        $projects = Campaign::published()
            ->ordered()
            ->withDonorsCount()
            ->with(['program', 'region'])
            ->take(8)
            ->get();

        $regions = Region::published()
            ->withCount(['campaigns' => $publishedCampaigns])
            ->with(['facilities' => fn ($facilities) => $facilities->published()])
            ->get();

        return view('home', [
            'homeSections' => collect(SiteSettings::homeSections())->where('visible', true)->pluck('key')->all(),
            'heroCampaigns' => $heroCampaigns,
            'quickGiveOptions' => SiteSettings::quickGiveOptions(),
            'regions' => $regions,
            'metrics' => ImpactMetric::approved()->orderBy('order')->take(4)->get(),
            'programs' => Program::published()->withCount(['campaigns' => $publishedCampaigns])->get(),
            'projects' => $projects,
            'stories' => Story::published()->take(4)->get(),
            'featuredCase' => SponsorshipCase::available()->longestWaiting()->with('region')->first(),
            'sponsorshipFrom' => SponsorshipCase::available()->min('monthly_amount'),
            'giftDesigns' => SiteSettings::giftDesigns(),
            'goldPricePerGram' => SiteSettings::goldPricePerGram(),
        ]);
    }
}
