<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Program;
use App\Models\Story;
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

        // Static public pages with their priorities.
        $staticPages = [
            ['route' => 'home', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['route' => 'about', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['route' => 'programs', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'campaigns', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['route' => 'impact', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'news', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['route' => 'sponsorship', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['route' => 'donate', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['route' => 'partners', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'volunteer', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'faq', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        // Dynamic content: use updated_at where available.
        $programs = Program::published()->get();
        $campaigns = Campaign::where('status', 'published')->orderBy('order')->get();
        $stories = Story::where('status', 'published')->latest('published_at')->get();

        $xml = $this->buildXml($staticPages, $programs, $campaigns, $stories, $lastmod);

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

    // ── Internals ───────────────────────────────────────────────────────────────

    private function buildXml(
        array $staticPages,
        $programs,
        $campaigns,
        $stories,
        string $fallbackLastmod,
    ): string {
        $locales = ['ar', 'en'];
        $lines = [];

        $lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $lines[] = '        xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        // Static pages
        foreach ($staticPages as $page) {
            $arUrl = route($page['route'], [], true);
            // English alternate — swap locale session by injecting ?locale param; use
            // the same route since our app is locale-session driven, not path-prefixed.
            $enUrl = $arUrl.(str_contains($arUrl, '?') ? '&' : '?').'lang=en';

            $lines[] = $this->urlEntry(
                loc: $arUrl,
                lastmod: $fallbackLastmod,
                changefreq: $page['changefreq'],
                priority: $page['priority'],
                alternates: [
                    'ar' => $arUrl,
                    'en' => $enUrl,
                ]
            );
        }

        // Programs
        foreach ($programs as $program) {
            $url = route('programs');
            $lines[] = $this->urlEntry(
                loc: $url,
                lastmod: $program->updated_at?->toAtomString() ?? $fallbackLastmod,
                changefreq: 'weekly',
                priority: '0.7',
            );
        }

        // Campaigns
        foreach ($campaigns as $campaign) {
            $url = route('campaigns');
            $lines[] = $this->urlEntry(
                loc: $url,
                lastmod: $campaign->updated_at?->toAtomString() ?? $fallbackLastmod,
                changefreq: 'daily',
                priority: '0.8',
            );
        }

        // Stories
        foreach ($stories as $story) {
            $url = route('news');
            $lines[] = $this->urlEntry(
                loc: $url,
                lastmod: $story->updated_at?->toAtomString() ?? $fallbackLastmod,
                changefreq: 'daily',
                priority: '0.6',
            );
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
            "    <loc>{$loc}</loc>",
            "    <lastmod>{$lastmod}</lastmod>",
            "    <changefreq>{$changefreq}</changefreq>",
            "    <priority>{$priority}</priority>",
        ];

        foreach ($alternates as $lang => $href) {
            $lines[] = "    <xhtml:link rel=\"alternate\" hreflang=\"{$lang}\" href=\"{$href}\"/>";
        }

        $lines[] = '  </url>';

        return implode("\n", $lines);
    }
}
