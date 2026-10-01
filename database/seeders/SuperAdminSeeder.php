<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    /**
     * Create the initial super administrator from the environment configuration.
     */
    public function run(): void
    {
        $email = config('auth.super_admin.email');
        $password = config('auth.super_admin.password');

        if (! is_string($email) || $email === '' || ! is_string($password) || strlen($password) < 12) {
            throw new RuntimeException('Set SUPER_ADMIN_EMAIL and a SUPER_ADMIN_PASSWORD of at least 12 characters before seeding.');
        }

        $superAdmin = User::query()
            ->where('role', User::ROLE_SUPER_ADMIN)
            ->orderBy('id')
            ->first();

        if ($superAdmin && User::where('email', $email)->where('id', '!=', $superAdmin->id)->exists()) {
            throw new RuntimeException('SUPER_ADMIN_EMAIL belongs to a different account.');
        }

        if ($superAdmin && $superAdmin->email !== $email) {
            $superAdmin->forceFill([
                'name' => config('auth.super_admin.name'),
                'email' => $email,
                'password' => $password,
            ])->save();
        }

        $superAdmin ??= User::firstOrCreate(
            ['email' => $email],
            [
                'name' => config('auth.super_admin.name'),
                'password' => $password,
                'role' => User::ROLE_SUPER_ADMIN,
            ],
        );

        if (! $superAdmin->isSuperAdmin()) {
            $superAdmin->forceFill([
                'name' => config('auth.super_admin.name'),
                'role' => User::ROLE_SUPER_ADMIN,
            ])->save();
        }

        User::query()
            ->where('role', User::ROLE_SUPER_ADMIN)
            ->where('id', '!=', $superAdmin->id)
            ->update(['role' => User::ROLE_ADMIN]);
    }
}
