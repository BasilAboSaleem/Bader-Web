<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_an_admin_account(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($superAdmin)->post(route('dashboard.users.store'), [
            'name' => 'Content Administrator',
            'email' => 'content-admin@example.com',
            'password' => 'a-secure-password-123',
            'password_confirmation' => 'a-secure-password-123',
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response->assertRedirect(route('dashboard.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Content Administrator',
            'email' => 'content-admin@example.com',
            'role' => User::ROLE_ADMIN,
        ]);
        $passwordHash = User::where('email', 'content-admin@example.com')->value('password');
        $this->assertTrue(Hash::check('a-secure-password-123', $passwordHash));
    }

    public function test_regular_admin_cannot_view_or_create_admin_accounts(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('dashboard.users.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('dashboard.users.store'), [
                'name' => 'Unauthorized Admin',
                'email' => 'unauthorized@example.com',
                'password' => 'a-secure-password-123',
                'password_confirmation' => 'a-secure-password-123',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'unauthorized@example.com']);
    }

    public function test_super_admin_is_created_from_configured_credentials(): void
    {
        $previousSuperAdmin = User::factory()->create([
            'email' => 'previous-super-admin@example.com',
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        config([
            'auth.super_admin.name' => 'Initial Super Admin',
            'auth.super_admin.email' => 'super-admin@example.com',
            'auth.super_admin.password' => 'configured-secure-password',
        ]);

        $this->seed(SuperAdminSeeder::class);

        $this->assertDatabaseHas('users', [
            'name' => 'Initial Super Admin',
            'email' => 'super-admin@example.com',
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $previousSuperAdmin->id,
            'email' => 'super-admin@example.com',
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $passwordHash = User::where('email', 'super-admin@example.com')->value('password');
        $this->assertTrue(Hash::check('configured-secure-password', $passwordHash));

        $this->post(route('login.store'), [
            'email' => 'super-admin@example.com',
            'password' => 'configured-secure-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs(User::where('email', 'super-admin@example.com')->first());
    }

    public function test_password_must_be_confirmed_and_at_least_twelve_characters(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($superAdmin)
            ->from(route('dashboard.users.create'))
            ->post(route('dashboard.users.store'), [
                'name' => 'Weak Account',
                'email' => 'weak@example.com',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseMissing('users', ['email' => 'weak@example.com']);
    }
}
