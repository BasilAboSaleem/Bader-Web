<?php

namespace App\Models;

use Database\Factories\SponsorRequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A sponsor asking the team to change an active sponsorship (stop it, or change the monthly amount).
 * Requests stay pending until an administrator approves or rejects them; approval is applied through SponsorshipManager.
 */
class SponsorRequest extends Model
{
    /** @use HasFactory<SponsorRequestFactory> */
    use HasFactory;

    public const TYPE_STOP = 'stop';

    public const TYPE_INCREASE = 'increase_amount';

    public const TYPE_DECREASE = 'decrease_amount';

    public const TYPES = [self::TYPE_STOP, self::TYPE_INCREASE, self::TYPE_DECREASE];

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED];

    protected $fillable = [
        'sponsorship_id',
        'sponsor_id',
        'type',
        'requested_amount',
        'currency',
        'message',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requested_amount' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(Sponsorship::class);
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
