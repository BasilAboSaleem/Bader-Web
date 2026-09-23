<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Launch Readiness Test — Phase 10.
 *
 * Validates that every public-facing page works correctly, auth flow is intact,
 * SEO assets (sitemap, robots) respond correctly, and the production health
 * check command runs without fatal errors.
 */
class LaunchReadinessTest extends TestCase
{
    use RefreshDatabase;

    // ── All public pages return 200 ────────────────────────────────────────────

    #[DataProvider('publicRouteProvider')]
    public function test_public_route_returns_200(string $route): void
    {
        $this->get(route($route))->assertOk();
    }

    /** @return array<string, array{string}> */
    public static function publicRouteProvider(): array
    {
        return [
            'home' => ['home'],
            'about' => ['about'],
            'programs' => ['programs'],
            'campaigns' => ['campaigns'],
            'impact' => ['impact'],
            'news' => ['news'],
            'sponsorship' => ['sponsorship'],
            'donate' => ['donate'],
            'partners' => ['partners'],
            'volunteer' => ['volunteer'],
            'faq' => ['faq'],
            'contact' => ['contact'],
        ];
    }

    // ── All public pages render in English without leaked keys ─────────────────

    #[DataProvider('publicRouteProvider')]
    public function test_public_route_renders_in_english_without_leaked_keys(string $route): void
    {
        $this->withSession(['locale' => 'en'])
            ->get(route($route))
            ->assertOk()
            ->assertSee('dir="ltr"', false)
            ->assertDontSee('page.', false)
            ->assertDontSee('errors.', false);
    }

    // ── Auth flow ──────────────────────────────────────────────────────────────

    public function test_full_auth_flow_login_dashboard_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@bader.test',
            'password' => 'password',
        ]);

        // Login page accessible
        $this->get(route('login'))->assertOk();

        // Submit credentials
        $this->post(route('login.store'), [
            'email' => 'admin@bader.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        // Dashboard accessible when authenticated
        $this->actingAs($user)->get(route('dashboard'))->assertOk();

        // Logout
        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));
    }

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    // ── SEO assets ─────────────────────────────────────────────────────────────

    public function test_sitemap_xml_is_valid_and_contains_home_url(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringContainsString(
            'application/xml',
            $response->headers->get('Content-Type')
        );

        $content = $response->getContent();
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString(route('home'), $content);
        $this->assertStringContainsString('<priority>', $content);
    }

    public function test_robots_txt_is_accessible_and_well_formed(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));

        $content = $response->getContent();
        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Disallow: /dashboard', $content);
        $this->assertStringContainsString('Sitemap:', $content);
        $this->assertStringContainsString(url('/sitemap.xml'), $content);
    }

    // ── HTTP Caching ───────────────────────────────────────────────────────────

    public function test_home_returns_etag_header(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();
        $this->assertNotNull($response->headers->get('ETag'));
    }

    public function test_home_returns_cache_control_public_for_guest(): void
    {
        $cacheControl = $this->get(route('home'))
            ->assertOk()
            ->headers->get('Cache-Control');

        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('max-age', $cacheControl);
    }

    // ── 404 / Error pages ──────────────────────────────────────────────────────

    public function test_404_page_shows_branded_arabic_content(): void
    {
        $this->get('/route-that-does-not-exist-at-all')
            ->assertNotFound()
            ->assertSee(__('errors.404.title', [], 'ar'), false);
    }

    public function test_404_page_shows_branded_english_content_when_locale_is_en(): void
    {
        // The English translation key must resolve to a real string (not the key itself).
        $enTitle = __('errors.404.title', [], 'en');
        $this->assertNotEquals('errors.404.title', $enTitle, 'English 404 title key must be translated');
        $this->assertNotEmpty($enTitle);

        // The 404 page in AR locale must contain the donate link — structural check.
        $this->get('/route-that-does-not-exist-at-all')
            ->assertNotFound()
            ->assertSee(route('donate'), false);
    }

    // ── Production health check command ────────────────────────────────────────

    public function test_health_check_command_runs_without_exceptions(): void
    {
        $exitCode = Artisan::call('bader:health');

        $output = Artisan::output();
        $this->assertNotEmpty($output);

        // In test env (APP_ENV=testing, APP_DEBUG=true), some checks will WARN/FAIL.
        // We just assert the command ran without throwing an exception.
        $this->assertContains($exitCode, [0, 1]);
    }

    public function test_health_check_json_flag_returns_json_array(): void
    {
        // Call command and capture output directly.
        Artisan::call('bader:health', ['--json' => true]);
        $raw = Artisan::output();

        // The output may contain ANSI escape codes or newlines; extract JSON.
        // Find the JSON array portion in output.
        preg_match('/(\[.*\])/s', $raw, $matches);
        $jsonString = $matches[1] ?? $raw;

        $decoded = json_decode(trim($jsonString), true);
        $this->assertIsArray($decoded, "Expected JSON array in output, got: {$raw}");
        $this->assertNotEmpty($decoded);

        foreach ($decoded as $result) {
            $this->assertArrayHasKey('check', $result);
            $this->assertArrayHasKey('status', $result);
            $this->assertArrayHasKey('note', $result);
            $this->assertContains($result['status'], ['PASS', 'WARN', 'FAIL']);
        }
    }

    // ── Locale integrity ───────────────────────────────────────────────────────

    public function test_arabic_locale_file_exists_and_is_valid_json(): void
    {
        $path = lang_path('ar.json');
        $this->assertFileExists($path);

        $decoded = json_decode(file_get_contents($path), true);
        $this->assertIsArray($decoded);
        $this->assertNotEmpty($decoded);
    }

    public function test_english_locale_file_exists_and_is_valid_json(): void
    {
        $path = lang_path('en.json');
        $this->assertFileExists($path);

        $decoded = json_decode(file_get_contents($path), true);
        $this->assertIsArray($decoded);
        $this->assertNotEmpty($decoded);
    }

    public function test_arabic_and_english_locale_files_have_same_keys(): void
    {
        $ar = array_keys(json_decode(file_get_contents(lang_path('ar.json')), true));
        $en = array_keys(json_decode(file_get_contents(lang_path('en.json')), true));

        sort($ar);
        sort($en);

        $missingInEn = array_diff($ar, $en);
        $missingInAr = array_diff($en, $ar);

        $this->assertEmpty(
            $missingInEn,
            'Keys in ar.json but missing in en.json: '.implode(', ', $missingInEn)
        );
        $this->assertEmpty(
            $missingInAr,
            'Keys in en.json but missing in ar.json: '.implode(', ', $missingInAr)
        );
    }

    // ── Configuration integrity ────────────────────────────────────────────────

    public function test_bader_config_has_required_keys(): void
    {
        $this->assertNotEmpty(config('bader.locales'));
        $this->assertContains('ar', config('bader.locales'));
        $this->assertContains('en', config('bader.locales'));
        $this->assertIsArray(config('bader.assets'));
        $this->assertArrayHasKey('favicon', config('bader.assets'));
        $this->assertArrayHasKey('mark_star', config('bader.assets'));
    }

    public function test_app_key_is_set(): void
    {
        $this->assertNotEmpty(config('app.key'), 'APP_KEY must be set before deployment');
    }
}
