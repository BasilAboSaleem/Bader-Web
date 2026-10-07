<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CompletedProject;
use App\Models\Facility;
use App\Models\Program;
use App\Models\Region;
use App\Models\Story;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    /**
     * Dynamic XML sitemap with bilingual alternates (ar / en).
     */
    public function index(): Response
    {
        $lastmod = Carbon::now()->toAtomString();

        $staticPages = [
            ['route' => 'home', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['route' => 'about', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['route' => 'programs', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'campaigns', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['route' => 'projects', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['route' => 'sponsorship', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'gift', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'zakat', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'impact', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'impact-map', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['route' => 'news', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['route' => 'donate', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['route' => 'partners', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'volunteer', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'faq', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        $detailPages = [
            ['route' => 'programs.show', 'models' => Program::published()->get(), 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['route' => 'campaigns.show', 'models' => Campaign::published()->ordered()->get(), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['route' => 'projects.show', 'models' => CompletedProject::published()->get(), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'regions.show', 'models' => Region::published()->get(), 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['route' => 'facilities.show', 'models' => Facility::published()->get(), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'news.show', 'models' => Story::published()->get(), 'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        $xml = $this->buildXml($staticPages, $detailPages, $lastmod);

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /**
     * Dynamic robots.txt.
     */
    public function robots(Request $request): Response
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = implode("\n", [
            'User-agent: *',
            'Disallow: /dashboard',
            'Disallow: /login',
            'Disallow: /logout',
            'Disallow: /locale/',
            'Allow: /',
            '',
            "Sitemap: {$sitemapUrl}",
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    /**
     * @param  list<array{route: string, priority: string, changefreq: string}>  $staticPages
     * @param  list<array{route: string, models: Collection, priority: string, changefreq: string}>  $detailPages
     */
    private function buildXml(array $staticPages, array $detailPages, string $fallbackLastmod): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
            '        xmlns:xhtml="http://www.w3.org/1999/xhtml">',
        ];

        foreach ($staticPages as $page) {
            $arUrl = route($page['route'], [], true);
            // The app is locale-session driven, so the English alternate reuses the same route.
            $enUrl = $arUrl.(str_contains($arUrl, '?') ? '&' : '?').'lang=en';

            $lines[] = $this->urlEntry(
                loc: $arUrl,
                lastmod: $fallbackLastmod,
                changefreq: $page['changefreq'],
                priority: $page['priority'],
                alternates: ['ar' => $arUrl, 'en' => $enUrl],
            );
        }

        foreach ($detailPages as $group) {
            foreach ($group['models'] as $model) {
                $lines[] = $this->urlEntry(
                    loc: route($group['route'], $model->key, true),
                    lastmod: $model->updated_at?->toAtomString() ?? $fallbackLastmod,
                    changefreq: $group['changefreq'],
                    priority: $group['priority'],
                );
            }
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines);
    }

    /**
     * Build a single <url> entry.
     *
     * @param  array<string, string>  $alternates
     */
    private function urlEntry(
        string $loc,
        string $lastmod,
        string $changefreq,
        string $priority,
        array $alternates = [],
    ): string {
        $lines = [
            '  <url>',
            '    <loc>'.e($loc).'</loc>',
            "    <lastmod>{$lastmod}</lastmod>",
            "    <changefreq>{$changefreq}</changefreq>",
            "    <priority>{$priority}</priority>",
        ];

        foreach ($alternates as $lang => $href) {
            $lines[] = '    <xhtml:link rel="alternate" hreflang="'.$lang.'" href="'.e($href).'"/>';
        }

        $lines[] = '  </url>';

        return implode("\n", $lines);
    }
}
