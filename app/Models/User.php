<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OTPHP\TOTP;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // ── Role constants ──────────────────────────────────────────────────────────
    const ROLE_ADMIN = 'admin';

    const ROLE_EDITOR = 'editor';

    const ROLE_VIEWER = 'viewer';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }

    // ── Role helpers ────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isEditor(): bool
    {
        return $this->role === self::ROLE_EDITOR;
    }

    public function isViewer(): bool
    {
        return $this->role === self::ROLE_VIEWER;
    }

    /** Admin and Editor can create / edit live content. */
    public function canManageContent(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_EDITOR], true);
    }

    /** Only Admins can touch site-wide settings and users. */
    public function canManageSettings(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // ── Two-Factor helpers ──────────────────────────────────────────────────────

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && $this->two_factor_confirmed_at !== null;
    }

    /**
     * Verify a TOTP code against the user's secret.
     * Requires the `spomky-labs/otphp` package; gracefully returns false when unavailable.
     */
    public function verifyTwoFactorCode(string $code): bool
    {
        if (! $this->hasTwoFactorEnabled() || empty($this->two_factor_secret)) {
            return false;
        }

        if (! class_exists(TOTP::class)) {
            return false;
        }

        $totp = TOTP::create($this->two_factor_secret);

        return $totp->verify($code);
    }
}
