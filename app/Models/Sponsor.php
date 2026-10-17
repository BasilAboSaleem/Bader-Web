<?php

namespace App\Models;

use Database\Factories\SponsorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Someone who sends the money for one or more orphans or widow families. Added by the team by hand;
 * a sponsor with an email address gets a dashboard account that only shows their own sponsorships.
 */
class Sponsor extends Model
{
    /** @use HasFactory<SponsorFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'preferred_currency',
        'notes',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::created(function (Sponsor $sponsor): void {
            $sponsor->forceFill(['code' => 'SPN-'.str_pad((string) $sponsor->id, 4, '0', STR_PAD_LEFT)])->saveQuietly();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sponsorships(): HasMany
    {
        return $this->hasMany(Sponsorship::class);
    }

    public function activeSponsorships(): HasMany
    {
        return $this->sponsorships()->active();
    }

    public function hasAccount(): bool
    {
        return $this->user_id !== null;
    }
}
