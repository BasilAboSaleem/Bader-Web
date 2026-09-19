<?php

namespace Tests\Feature;

use App\Support\PublicNavigation;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    public function test_home_renders_brand_structure(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(__('brand.hq'), false)
            ->assertSee(__('brand.field'), false)
            ->assertSee(__('nav.donate'), false)
            ->assertSee(__('home.hero_title'), false)
            ->assertSee(__('campaign.labbayk'), false)
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
            ->assertSee('Headquarters in Muscat', false)
            ->assertSee('Field operations: Gaza', false);
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
                ->assertSee(__($item['key']), false);
        }

        $this->get(route('donate'))
            ->assertOk()
            ->assertSee(__('nav.donate'), false);
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
