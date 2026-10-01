<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Region;
use App\Models\SponsorshipCase;
use App\Models\Story;
use App\Support\PublicNavigation;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_the_giving_sections_from_published_content(): void
    {
        $region = Region::factory()->create();
        $campaign = Campaign::factory()->featured()->create(['region_id' => $region->id, 'goal_amount' => 120000]);
        $draftCampaign = Campaign::factory()->featured()->draft()->create();
        $case = SponsorshipCase::factory()->create(['region_id' => $region->id]);
        $story = Story::create([
            'key' => 'field-update',
            'title_ar' => 'تحديث من الميدان',
            'title_en' => 'Field update',
            'published_at' => now()->toDateString(),
            'status' => 'published',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee(SiteSettings::hqLocation(), false)
            ->assertSee(SiteSettings::fieldLocation(), false)
            ->assertSee(__('home.quick_give.title'), false)
            ->assertSee($campaign->title_ar, false)
            ->assertSee('$120,000', false)
            ->assertSee($region->name_ar, false)
            ->assertSee('id="regions-map"', false)
            ->assertSee($case->name_ar, false)
            ->assertSee($story->title_ar, false)
            ->assertSee('data-zakat', false)
            ->assertViewHas('heroCampaigns', fn ($campaigns): bool => ! $campaigns->contains($draftCampaign));
    }

    public function test_home_renders_without_any_published_content(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(__('home.hero_title'), false)
            ->assertSee(__('page.news.empty'), false)
            ->assertSee(__('header.no_waiting_cases'), false);
    }

    public function test_language_can_switch_to_english(): void
    {
        Campaign::factory()->featured()->create(['title_en' => 'Clean Water Wells']);

        $this->from(route('home'))
            ->get(route('locale.switch', 'en'))
            ->assertRedirect(route('home'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('dir="ltr"', false)
            ->assertSee(SiteSettings::hqLocation('en'), false)
            ->assertSee(SiteSettings::fieldLocation('en'), false)
            ->assertSee('Clean Water Wells', false)
            ->assertDontSee('home.', false);
    }

    public function test_language_switch_preserves_the_current_public_page(): void
    {
        $this->from(route('programs'))
            ->get(route('locale.switch', 'en'))
            ->assertRedirect(route('programs'));

        $this->get(route('programs'))
            ->assertOk()
            ->assertSee('dir="ltr"', false)
            ->assertSee('Relief and development fields', false)
            ->assertSee('Economic empowerment', false)
            ->assertDontSee('program.', false);
    }

    public function test_invalid_locale_is_not_found(): void
    {
        $this->get('/locale/fr')->assertNotFound();
    }

    public function test_public_pages_are_reachable(): void
    {
        foreach (PublicNavigation::all() as $item) {
            $this->get(route($item['route']))
                ->assertOk()
                ->assertSee(__($item['key']), false)
                ->assertDontSee(__('stub.body'), false)
                ->assertDontSee('page.', false)
                ->assertDontSee('campaign.education', false);
        }

        $this->get(route('donate'))
            ->assertOk()
            ->assertSee(__('nav.donate'), false);
    }

    public function test_all_public_pages_render_in_english_without_translation_keys(): void
    {
        foreach (PublicNavigation::all() as $item) {
            $this->withSession(['locale' => 'en'])
                ->get(route($item['route']))
                ->assertOk()
                ->assertSee('dir="ltr"', false)
                ->assertSee(__($item['key'], [], 'en'), false)
                ->assertDontSee('page.', false)
                ->assertDontSee('program.', false)
                ->assertDontSee('story.', false);
        }
    }

    public function test_not_found_page_uses_site_layout(): void
    {
        $this->get('/page-does-not-exist')
            ->assertNotFound()
            ->assertSee(__('errors.404.title'), false)
            ->assertSee(__('nav.donate'), false);
    }

    public function test_single_news_story_renders_with_details_and_related_content(): void
    {
        $story = Story::create([
            'key' => 'gaza-water-distribution',
            'title_ar' => 'توزيع مياه صالحة للشرب في شمال غزة',
            'title_en' => 'Clean Water Distribution in North Gaza',
            'excerpt_ar' => 'ملخص خبر توزيع المياه.',
            'excerpt_en' => 'Water distribution summary.',
            'content_ar' => 'تفاصيل موسعة حول سير عمليات التوزيع للمواطنين والنازحين في قطاع غزة.',
            'content_en' => 'Extended details regarding distribution operations to citizens in Gaza.',
            'category_ar' => 'إغاثة مائية',
            'category_en' => 'Water Relief',
            'published_at' => now()->toDateString(),
            'status' => 'published',
        ]);

        $this->get(route('news.show', $story->key))
            ->assertOk()
            ->assertSee('توزيع مياه صالحة للشرب في شمال غزة', false)
            ->assertSee('ملخص خبر توزيع المياه.', false)
            ->assertSee('تفاصيل موسعة حول سير عمليات التوزيع', false)
            ->assertSee('إغاثة مائية', false);

        // Test in English
        $this->withSession(['locale' => 'en'])
            ->get(route('news.show', $story->key))
            ->assertOk()
            ->assertSee('Clean Water Distribution in North Gaza', false)
            ->assertSee('Extended details regarding distribution operations', false);
    }
}
