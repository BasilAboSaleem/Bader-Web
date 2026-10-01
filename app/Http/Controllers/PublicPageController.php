<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Facility;
use App\Models\Program;
use App\Models\Story;
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
        $programs = Program::published()->get();
        if ($programs->isEmpty()) {
            $programs = [
                'water', 'food', 'shelter', 'winter', 'health', 'education',
                'social_protection', 'economic_empowerment', 'community_partnerships', 'zakat', 'sacrifices',
            ];
        }

        return view('pages.programs', compact('programs'));
    }

    public function campaigns(): View
    {
        $campaigns = Campaign::published()->get();
        if ($campaigns->isEmpty()) {
            $campaigns = [
                ['key' => 'water', 'goal' => '120,000', 'currency' => 'campaign.currency'],
                ['key' => 'education', 'goal' => null, 'currency' => null],
            ];
        }

        return view('pages.campaigns', compact('campaigns'));
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
        $stories = Story::published()->paginate(12);

        return view('pages.news', compact('stories'));
    }

    public function newsShow(string $key): View
    {
        $story = Story::where('status', 'published')
            ->where(function ($query) use ($key) {
                $query->where('key', $key);
                if (is_numeric($key)) {
                    $query->orWhere('id', (int) $key);
                }
            })
            ->firstOrFail();

        $prevStory = Story::published()
            ->where('id', '!=', $story->id)
            ->where(function ($q) use ($story) {
                if ($story->published_at) {
                    $q->where('published_at', '<=', $story->published_at);
                }
            })
            ->latest('published_at')
            ->first();

        $nextStory = Story::published()
            ->where('id', '!=', $story->id)
            ->where(function ($q) use ($story) {
                if ($story->published_at) {
                    $q->where('published_at', '>=', $story->published_at);
                }
            })
            ->oldest('published_at')
            ->first();

        $relatedStories = Story::published()
            ->where('id', '!=', $story->id)
            ->take(3)
            ->get();

        return view('pages.news-show', compact('story', 'relatedStories', 'prevStory', 'nextStory'));
    }

    public function facilityShow(string $key): View
    {
        $facility = Facility::where('status', 'published')
            ->where(function ($query) use ($key) {
                $query->where('key', $key);
                if (is_numeric($key)) {
                    $query->orWhere('id', (int) $key);
                }
            })
            ->firstOrFail();

        $relatedFacilities = Facility::published()
            ->where('id', '!=', $facility->id)
            ->take(3)
            ->get();

        return view('pages.facility-show', compact('facility', 'relatedFacilities'));
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
        $campaigns = Campaign::published()->get();
        $programs = Program::published()->get();
        $facilities = Facility::published()->get();

        return view('pages.donate', [
            'campaignKey' => 'water',
            'campaigns' => $campaigns,
            'programs' => $programs,
            'facilities' => $facilities,
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
