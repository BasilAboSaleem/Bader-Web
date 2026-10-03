<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_home_links_every_content_section(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('dashboard'))->assertOk();

        foreach ([
            'dashboard.pages.edit', 'dashboard.site-texts.edit', 'dashboard.faqs.index', 'dashboard.impact.index', 'dashboard.media.index',
            'dashboard.programs.index', 'dashboard.campaigns.index', 'dashboard.facilities.index', 'dashboard.regions.index',
            'dashboard.sponsorship-cases.index', 'dashboard.stories.index', 'dashboard.inbox.index', 'dashboard.donations.index',
            'dashboard.settings.edit',
        ] as $route) {
            $response->assertSee('href="'.route($route).'"', false);
        }

        $response->assertDontSee('href="'.route('dashboard.users.index').'"', false);
    }

    public function test_super_admin_sees_the_accounts_section(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($superAdmin)
            ->get(route('dashboard.programs.index'))
            ->assertOk()
            ->assertSee('href="'.route('dashboard.users.index').'"', false);
    }

    public function test_dashboard_counts_only_items_waiting_for_the_team(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        foreach (['unread', 'unread', 'resolved'] as $status) {
            FormSubmission::create(['type' => 'contact', 'name' => 'زائر', 'email' => 'visitor@example.com', 'message' => 'رسالة', 'status' => $status]);
        }

        foreach (['pending', 'verified', 'verified'] as $status) {
            Donation::create(['donor_name' => 'متبرع', 'amount' => 100, 'status' => $status, 'payment_method' => 'bank_transfer']);
        }

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('stats', fn (array $stats): bool => collect($stats)->pluck('value', 'key')->all() === [
                'unread_inbox' => '2',
                'pending_donations' => '1',
                'verified_total' => '$200',
                'published_campaigns' => '0',
            ]);
    }
}
