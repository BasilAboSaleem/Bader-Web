<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OTPHP\TOTP;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // ── Role constants ──────────────────────────────────────────────────────────
    const ROLE_SUPER_ADMIN = 'super_admin';

    const ROLE_ADMIN = 'admin';

    const ROLE_EDITOR = 'editor';

    const ROLE_VIEWER = 'viewer';

    /** Board member: read-only orphan case files and monthly finance reports. */
    const ROLE_BOARD_MEMBER = 'board_member';

    /** An outside sponsor: signs in only to follow their own sponsored orphans and reports. */
    const ROLE_SPONSOR = 'sponsor';

    /** Roles that may create and edit live content. */
    const CONTENT_ROLES = [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_EDITOR];

    /** Staff who manage the public site and operations (excludes board members). */
    const DASHBOARD_MANAGE_ROLES = [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_EDITOR, self::ROLE_VIEWER];

    /** The organisation's own team, the only accounts allowed into the dashboard. */
    const STAFF_ROLES = [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_EDITOR, self::ROLE_VIEWER, self::ROLE_BOARD_MEMBER];

    /** Roles that may open orphan case files (full details, including read-only board access). */
    const ORPHAN_VIEW_ROLES = [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_EDITOR, self::ROLE_BOARD_MEMBER];

    /** Roles that manage sponsors, sponsorship amounts and the money paid out to orphans. */
    const FINANCE_ROLES = [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN];

    /** Roles that may open the monthly finance reports (including read-only board access). */
    const FINANCE_REPORT_ROLES = [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_BOARD_MEMBER];

    /** Accounts the super admin may create from the users screen. */
    const MANAGED_ROLES = [self::ROLE_ADMIN, self::ROLE_BOARD_MEMBER];

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

    /** The single owner account that manages the other administrators. */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

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

    public function isBoardMember(): bool
    {
        return $this->role === self::ROLE_BOARD_MEMBER;
    }

    /** Admin and Editor can create / edit live content. */
    public function canManageContent(): bool
    {
        return in_array($this->role, self::CONTENT_ROLES, true);
    }

    /** Only Admins (and the Super Admin) can touch site-wide settings. */
    public function canManageSettings(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN], true);
    }

    public function isSponsor(): bool
    {
        return $this->role === self::ROLE_SPONSOR;
    }

    public function isStaff(): bool
    {
        return in_array($this->role, self::STAFF_ROLES, true);
    }

    public function canManageFinance(): bool
    {
        return in_array($this->role, self::FINANCE_ROLES, true);
    }

    /** Open orphan case files with full details (board members: view only). */
    public function canViewOrphans(): bool
    {
        return in_array($this->role, self::ORPHAN_VIEW_ROLES, true);
    }

    /** Create, edit, review and delete orphan case files. */
    public function canManageOrphans(): bool
    {
        return in_array($this->role, self::CONTENT_ROLES, true);
    }

    /** Open monthly finance reports and export them as PDF (board members: view only). */
    public function canViewFinanceReports(): bool
    {
        return in_array($this->role, self::FINANCE_REPORT_ROLES, true);
    }

    /** The sponsor record behind a sponsor account. */
    public function sponsor(): HasOne
    {
        return $this->hasOne(Sponsor::class);
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
