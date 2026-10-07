<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CompletedProject;
use App\Models\Donation;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\Program;
use App\Models\Region;
use App\Models\SponsorshipCase;
use App\Models\Story;
use App\Services\StripePaymentService;
use App\Support\SiteSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function about(): View
    {
        return $this->page('about');
    }

    public function programs(): View
    {
        $programs = Program::published()->withProjectsCount()->get();

        $fallbackPrograms = $programs->isEmpty() ? [
            'water', 'food', 'shelter', 'winter', 'health', 'education',
            'social_protection', 'economic_empowerment', 'community_partnerships', 'zakat', 'sacrifices',
        ] : [];

        return view('pages.programs', compact('programs', 'fallbackPrograms'));
    }

    public function programShow(string $key): View
    {
        $program = Program::published()->where('key', $key)->firstOrFail();

        $campaigns = $program->campaigns()
            ->published()
            ->ordered()
            ->withDonorsCount()
            ->with('program')
            ->get();

        $completedProjects = $program->completedProjects()->published()->with('region')->get();

        $stories = $program->stories()->published()->take(6)->get();

        $otherPrograms = Program::published()
            ->whereKeyNot($program->getKey())
            ->withProjectsCount()
            ->take(4)
            ->get();

        return view('pages.program-show', compact('program', 'campaigns', 'completedProjects', 'stories', 'otherPrograms'));
    }

    /**
     * All published projects, filterable by program and region.
     */
    public function completedProjects(Request $request): View
    {
        $programs = Program::published()->get();
        $regions = Region::published()->get();

        $selectedProgram = $programs->firstWhere('key', $request->query('program'));
        $selectedRegion = $regions->firstWhere('key', $request->query('region'));

        $filteredProjects = CompletedProject::published()
            ->when($selectedProgram, fn (Builder $query) => $query->where('program_id', $selectedProgram->id))
            ->when($selectedRegion, fn (Builder $query) => $query->where('region_id', $selectedRegion->id));

        $totals = (clone $filteredProjects)->reorder()->toBase()
            ->selectRaw('count(*) as projects_count, coalesce(sum(beneficiaries), 0) as beneficiaries_total')
            ->first();

        $completedProjects = $filteredProjects
            ->with(['program', 'region'])
            ->paginate(12)
            ->withQueryString();

        return view('pages.completed-projects', compact('completedProjects', 'programs', 'regions', 'selectedProgram', 'selectedRegion', 'totals'));
    }

    public function completedProjectShow(string $key): View
    {
        $completedProject = CompletedProject::published()
            ->where('key', $key)
            ->with(['program', 'region'])
            ->firstOrFail();

        $relatedProjects = CompletedProject::published()
            ->whereKeyNot($completedProject->getKey())
            ->when($completedProject->program_id, fn (Builder $query) => $query->orderByRaw('program_id = ? desc', [$completedProject->program_id]))
            ->with(['program', 'region'])
            ->take(3)
            ->get();

        return view('pages.completed-project-show', compact('completedProject', 'relatedProjects'));
    }

    public function campaigns(Request $request): View
    {
        $programs = Program::published()->get();

        $selectedProgram = $programs->firstWhere('key', $request->query('program'));

        $campaigns = Campaign::published()
            ->ordered()
            ->withDonorsCount()
            ->with('program')
            ->when($selectedProgram, fn (Builder $query) => $query->where('program_id', $selectedProgram->id))
            ->paginate(12)
            ->withQueryString();

        return view('pages.campaigns', compact('campaigns', 'programs', 'selectedProgram'));
    }

    public function campaignShow(string $key): View
    {
        $campaign = Campaign::published()
            ->where('key', $key)
            ->withDonorsCount()
            ->with('program')
            ->firstOrFail();

        $relatedCampaigns = Campaign::published()
            ->whereKeyNot($campaign->getKey())
            ->when($campaign->program_id, fn (Builder $query) => $query->orderByRaw('program_id = ? desc', [$campaign->program_id]))
            ->ordered()
            ->withDonorsCount()
            ->with('program')
            ->take(3)
            ->get();

        return view('pages.campaign-show', compact('campaign', 'relatedCampaigns'));
    }

    public function regionShow(string $key): View
    {
        $region = Region::published()
            ->where('key', $key)
            ->with(['facilities' => fn ($facilities) => $facilities->published()])
            ->firstOrFail();

        $completedProjects = $region->completedProjects()->published()->with(['program', 'region'])->get();

        $cases = $region->sponsorshipCases()
            ->available()
            ->longestWaiting()
            ->with('region')
            ->take(3)
            ->get();

        $otherRegions = Region::published()->whereKeyNot($region->getKey())->get();

        return view('pages.region-show', compact('region', 'completedProjects', 'cases', 'otherRegions'));
    }

    /**
     * Full-size impact map; "?region=key" opens it on that region (used by the share links).
     */
    public function impactMap(Request $request): View
    {
        $regions = Region::forImpactMap()->get();
        $selectedRegion = $regions->firstWhere('key', $request->query('region'));

        return view('pages.impact-map', compact('regions', 'selectedRegion'));
    }

    public function impact(): View
    {
        return $this->page('impact');
    }

    public function news(): View
    {
        $stories = Story::published()->paginate(12);

        return view('pages.news', compact('stories'));
    }

    public function newsShow(string $key): View
    {
        $story = Story::where('status', 'published')
            ->with(['program' => fn ($program) => $program->where('status', 'published')])
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

        $relatedStories = Story::query()
            ->where('status', 'published')
            ->where('id', '!=', $story->id)
            ->when($story->program_id, fn (Builder $query) => $query->orderByRaw('program_id = ? desc', [$story->program_id]))
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('pages.news-show', compact('story', 'relatedStories', 'prevStory', 'nextStory'));
    }

    public function facilityShow(string $key): View
    {
        $facility = Facility::where('status', 'published')
            ->with('region')
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

    /**
     * Donate form, prefilled from the query string (target, amount, frequency, category and gift card fields).
     */
    public function donate(Request $request, StripePaymentService $stripe): View
    {
        $query = fn (string $key): string => is_string($value = $request->query($key)) ? trim($value) : '';

        $target = $this->donationTarget($query('target_type'), is_numeric($query('target_id')) ? (int) $query('target_id') : null);

        $requestedAmount = is_numeric($query('amount')) ? (float) $query('amount') : 0;
        $amount = $requestedAmount >= 1 && $requestedAmount <= 1000000
            ? $requestedAmount + 0
            : ($target['amounts'][1] ?? $target['amounts'][0]);

        $defaultFrequency = $target['type'] === 'sponsorship' ? Donation::FREQUENCY_MONTHLY : Donation::FREQUENCY_ONCE;
        $frequency = in_array($query('frequency'), [Donation::FREQUENCY_ONCE, Donation::FREQUENCY_MONTHLY], true) ? $query('frequency') : $defaultFrequency;
        if (! $target['allows_monthly']) {
            $frequency = Donation::FREQUENCY_ONCE;
        }

        $categories = SiteSettings::donationCategories();
        $category = array_key_exists($query('category'), $categories) ? $query('category') : 'general';

        $giftDesigns = SiteSettings::giftDesigns();

        return view('pages.donate', [
            'target' => $target,
            'amount' => $amount,
            'frequency' => $frequency,
            'category' => $category,
            'categories' => $categories,
            'paymentMethods' => $stripe->isAvailable() ? ['stripe', 'bank_transfer'] : ['bank_transfer'],
            'giftDesigns' => $giftDesigns,
            'gift' => [
                'enabled' => $request->boolean('gift'),
                'design' => array_key_exists($query('gift_design'), $giftDesigns) ? $query('gift_design') : array_key_first($giftDesigns),
                'recipient_name' => Str::limit($query('gift_recipient_name'), 255, ''),
                'recipient_contact' => Str::limit($query('gift_recipient_contact'), 255, ''),
                'sender_name' => Str::limit($query('gift_sender_name'), 255, ''),
                'message' => Str::limit($query('gift_message'), 500, ''),
            ],
        ]);
    }

    public function gift(Request $request): View
    {
        $target = $this->donationTarget(
            is_string($request->query('target_type')) ? $request->query('target_type') : '',
            is_numeric($request->query('target_id')) ? (int) $request->query('target_id') : null,
        );

        return view('pages.gift', [
            'target' => $target,
            'giftDesigns' => SiteSettings::giftDesigns(),
            'categories' => SiteSettings::donationCategories(),
        ]);
    }

    public function zakat(): View
    {
        return view('pages.zakat', [
            'goldPricePerGram' => SiteSettings::goldPricePerGram(),
            'nisabGrams' => config('bader.zakat.nisab_gold_grams'),
        ]);
    }

    public function partners(): View
    {
        return $this->page('partners');
    }

    public function volunteer(): View
    {
        return $this->page('volunteer');
    }

    public function faq(): View
    {
        return view('pages.faq', [
            'faqs' => Faq::published()->get(),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    private function page(string $key): View
    {
        return view('pages.public', [
            'key' => $key,
            'cards' => SiteSettings::institutionalCards($key),
        ]);
    }

    /**
     * Resolve a public donation target; unknown or unpublished targets fall back to general giving.
     *
     * @return array{type: string, id: ?int, title: string, description: ?string, image: ?string, url: ?string, amounts: list<int|float>, allows_monthly: bool}
     */
    private function donationTarget(string $type, ?int $id): array
    {
        $general = [
            'type' => 'general',
            'id' => null,
            'title' => __('donation.target_general'),
            'description' => __('donate_page.general_description'),
            'image' => null,
            'url' => null,
            'amounts' => Campaign::DEFAULT_PRESET_AMOUNTS,
            'allows_monthly' => true,
        ];

        if ($id === null) {
            return $general;
        }

        $model = match ($type) {
            'campaign' => Campaign::published()->find($id),
            'program' => Program::published()->find($id),
            'facility' => Facility::published()->find($id),
            'sponsorship' => SponsorshipCase::available()->find($id),
            default => null,
        };

        if ($model === null) {
            return $general;
        }

        return match (true) {
            $model instanceof Campaign => [
                'type' => 'campaign',
                'id' => $model->id,
                'title' => $model->title,
                'description' => $model->description,
                'image' => $model->image,
                'url' => route('campaigns.show', $model->key),
                'amounts' => $model->amount_options,
                'allows_monthly' => $model->allows_monthly,
            ],
            $model instanceof Program => [
                'type' => 'program',
                'id' => $model->id,
                'title' => $model->title,
                'description' => $model->description,
                'image' => $model->image,
                'url' => route('programs.show', $model->key),
                'amounts' => Campaign::DEFAULT_PRESET_AMOUNTS,
                'allows_monthly' => true,
            ],
            $model instanceof Facility => [
                'type' => 'facility',
                'id' => $model->id,
                'title' => $model->name,
                'description' => $model->description,
                'image' => $model->image ?? null,
                'url' => route('facilities.show', $model->key),
                'amounts' => Campaign::DEFAULT_PRESET_AMOUNTS,
                'allows_monthly' => true,
            ],
            default => [
                'type' => 'sponsorship',
                'id' => $model->id,
                'title' => __('sponsorship.donation_target', ['name' => $model->name, 'code' => $model->code]),
                'description' => $model->bio,
                'image' => $model->photo,
                'url' => route('sponsorship.show', $model->code),
                'amounts' => [$model->monthly_amount + 0],
                'allows_monthly' => true,
            ],
        };
    }
}
