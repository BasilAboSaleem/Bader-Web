<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertSee(__('auth.login_heading'), false)
            ->assertSee(__('brand.name'), false)
            ->assertSee(__('auth.login_button'), false)
            ->assertSee('dir="rtl"', false);
    }

    public function test_authenticated_user_is_redirected_from_login_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_can_authenticate_using_login_screen(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_can_view_dashboard_with_bader_branding_and_modules(): void
    {
        $user = User::factory()->create([
            'name' => 'باسل أبو سليم',
            'email' => 'admin@baderhumanitarian.com',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('باسل أبو سليم', false)
            ->assertSee(__('brand.name'), false)
            ->assertSee(__('dashboard.team_area'), false)
            ->assertSee(__('dashboard.module.site_settings'), false)
            ->assertSee(__('dashboard.module.programs'), false)
            ->assertSee(__('dashboard.module.campaigns'), false)
            ->assertSee(__('dashboard.module.inbox'), false)
            ->assertSee(__('dashboard.logout'), false)
            ->assertSee(__('dashboard.view_site'), false)
            ->assertSee('dir="rtl"', false);
    }

    public function test_dashboard_does_not_contain_tailadmin_commercial_or_demo_elements(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertDontSee('eCommerce', false)
            ->assertDontSee('TailAdmin', false)
            ->assertDontSee('Pro Version', false)
            ->assertDontSee('Calendar Demo', false)
            ->assertDontSee('Store Admin', false);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
