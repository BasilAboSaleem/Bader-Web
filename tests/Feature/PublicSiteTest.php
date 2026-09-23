<?php

namespace Tests\Feature;

use App\Support\PublicNavigation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_brand_structure(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(__('brand.hq'), false)
            ->assertSee(__('brand.field'), false)
            ->assertSee(__('nav.donate'), false)
            ->assertSee(__('home.hero_title'), false)
            ->assertSee(__('campaign.water'), false)
            ->assertSee(__('campaign.goal'), false)
            ->assertSee('120,000', false)
            ->assertSee(__('home.stories_title'), false)
            ->assertSee(__('story.quran_honor.title'), false)
            ->assertSee('data-home-section="stories"', false)
            ->assertSee('bg-bader-green-deep', false)
            ->assertDontSee('border-white/10 bg-black/20', false)
            ->assertDontSee('10400', false)
            ->assertSee('dir="rtl"', false);
    }

    public function test_language_can_switch_to_english(): void
    {
        $this->from(route('home'))
            ->get(route('locale.switch', 'en'))
            ->assertRedirect(route('home'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('dir="ltr"', false)
            ->assertSee('Sultanate of Oman, Muscat', false)
            ->assertSee('Palestine, Gaza Strip', false)
            ->assertSee(__('campaign.water'), false);
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

    public function test_urgent_bar_stays_hidden_until_enabled(): void
    {
        $this->get(route('home'))
            ->assertDontSee('winter-appeal-test', false);

        config([
            'bader.urgent.enabled' => true,
            'bader.urgent.text' => 'winter-appeal-test',
            'bader.urgent.url' => '/donate',
        ]);

        $this->get(route('home'))
            ->assertSee('winter-appeal-test', false);
    }
}
