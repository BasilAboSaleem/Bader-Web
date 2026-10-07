<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CompletedProject;
use App\Models\Donation;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\FormSubmission;
use App\Models\ImpactMetric;
use App\Models\MediaAsset;
use App\Models\Program;
use App\Models\Region;
use App\Models\SponsorshipCase;
use App\Models\Story;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;
use Tests\TestCase;

/**
 * Opens every GET page of the public site (both languages) and the dashboard with realistic content,
 * so a broken view, missing relation or leaked translation key anywhere fails the suite.
 */
class FullSiteSmokeTest extends TestCase
{
    use RefreshDatabase;

    /** Route parameter name => model whose first record is used to build the URL. */
    private const DASHBOARD_BINDINGS = [
        'campaign' => Campaign::class,
        'completedProject' => CompletedProject::class,
        'donation' => Donation::class,
        'facility' => Facility::class,
        'faq' => Faq::class,
        'impact' => ImpactMetric::class,
        'inbox' => FormSubmission::class,
        'medium' => MediaAsset::class,
        'program' => Program::class,
        'region' => Region::class,
        'sponsorship_case' => SponsorshipCase::class,
        'story' => Story::class,
    ];

    private const LEAKED_KEY_PATTERN = '/>\s*(home|nav|footer|header|page|common|cta|brand|donation|donate_page|donate_box|gift_page|zakat_page|sponsorship_page|sponsorship|faq_page|about_page|contact_page|program_page|campaign_page|campaigns_page|region_page|facility_page|completed_project_page|media|story_page|news_page|form|form_page|errors|project|dashboard)\.[a-z0-9_.]+\s*</';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ContentSeeder::class);

        $region = Region::query()->firstOrFail();
        SponsorshipCase::factory()->create(['region_id' => $region->id]);
        Facility::query()->update(['region_id' => $region->id]);
        CompletedProject::factory()->create([
            'region_id' => $region->id,
            'program_id' => Program::query()->value('id'),
            'gallery' => ['images/programs/water.jpg', 'images/programs/food.jpg'],
            'videos' => ['https://youtu.be/dQw4w9WgXcQ'],
        ]);

        Donation::create([
            'donor_name' => 'متبرع تجريبي',
            'donor_email' => 'donor@example.com',
            'amount' => 50,
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);

        FormSubmission::create([
            'type' => 'contact',
            'name' => 'زائر',
            'email' => 'visitor@example.com',
            'message' => 'رسالة تجريبية',
        ]);
    }

    public function test_every_dashboard_page_renders_for_the_super_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        foreach ($this->getRoutes(fn (string $name): bool => str_starts_with($name, 'dashboard')) as $route) {
            $parameters = [];

            foreach ($route->parameterNames() as $parameter) {
                $model = self::DASHBOARD_BINDINGS[$parameter] ?? null;
                $this->assertNotNull($model, "No test binding for {{$parameter}} in {$route->getName()}");
                $parameters[$parameter] = $model::query()->firstOrFail()->getRouteKey();
            }

            $response = $this->actingAs($superAdmin)->get(route($route->getName(), $parameters));

            $this->assertSame(200, $response->status(), "{$route->getName()} returned {$response->status()}");
            $this->assertDoesNotMatchRegularExpression(self::LEAKED_KEY_PATTERN, $this->withoutCodeSnippets($response->getContent()), "{$route->getName()} shows a raw translation key");
        }
    }

    public function test_every_public_page_renders_in_arabic_and_english(): void
    {
        $parameters = [
            'campaigns.show' => ['key' => Campaign::published()->firstOrFail()->key],
            'projects.show' => ['key' => CompletedProject::published()->firstOrFail()->key],
            'programs.show' => ['key' => Program::published()->firstOrFail()->key],
            'regions.show' => ['key' => Region::published()->firstOrFail()->key],
            'facilities.show' => ['key' => Facility::published()->firstOrFail()->key],
            'news.show' => ['key' => Story::published()->firstOrFail()->key],
            'sponsorship.show' => ['code' => SponsorshipCase::query()->firstOrFail()->code],
        ];
        $skipped = ['locale.switch', 'donate.success', 'donate.cancel', 'sitemap', 'robots', 'login'];

        $routes = $this->getRoutes(fn (string $name): bool => ! str_starts_with($name, 'dashboard') && ! in_array($name, $skipped, true));

        foreach (['ar', 'en'] as $locale) {
            foreach ($routes as $route) {
                $response = $this->withSession(['locale' => $locale])->get(route($route->getName(), $parameters[$route->getName()] ?? []));

                $this->assertSame(200, $response->status(), "[{$locale}] {$route->getName()} returned {$response->status()}");
                $this->assertDoesNotMatchRegularExpression(self::LEAKED_KEY_PATTERN, $response->getContent(), "[{$locale}] {$route->getName()} shows a raw translation key");
            }
        }
    }

    public function test_regular_admin_can_open_content_pages_but_not_administrator_accounts(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get(route('dashboard.settings.edit'))->assertOk();
        $this->actingAs($admin)->get(route('dashboard.users.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertDontSee(route('dashboard.users.index'), false);
    }

    public function test_super_admin_can_remove_an_administrator_but_not_themselves(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($superAdmin)
            ->delete(route('dashboard.users.destroy', $admin))
            ->assertRedirect(route('dashboard.users.index'));
        $this->assertModelMissing($admin);

        $this->actingAs($superAdmin)
            ->delete(route('dashboard.users.destroy', $superAdmin))
            ->assertForbidden();
        $this->assertModelExists($superAdmin);
    }

    /**
     * The site texts editor intentionally shows each translation key inside a <code> badge.
     */
    private function withoutCodeSnippets(string $html): string
    {
        return preg_replace('#<code\b[^>]*>.*?</code>#s', '', $html);
    }

    /**
     * @param  callable(string): bool  $filter
     * @return list<Route>
     */
    private function getRoutes(callable $filter): array
    {
        return array_values(array_filter(
            Router::getRoutes()->getRoutes(),
            fn (Route $route): bool => in_array('GET', $route->methods(), true)
                && $route->getName() !== null
                && ! str_starts_with($route->uri(), '_')
                && $route->getName() !== 'storage.local'
                && $filter($route->getName()),
        ));
    }
}
