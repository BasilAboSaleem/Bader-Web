<?php

namespace App\Models;

use Database\Factories\SponsorshipCaseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SponsorshipCase extends Model
{
    /** @use HasFactory<SponsorshipCaseFactory> */
    use HasFactory;

    public const TYPES = ['orphan', 'widow', 'student'];

    public const STATUS_AVAILABLE = 'available';

    public const STATUS_SPONSORED = 'sponsored';

    public const STATUS_HIDDEN = 'hidden';

    protected $fillable = [
        'code',
        'type',
        'name_ar',
        'name_en',
        'age',
        'gender',
        'region_id',
        'monthly_amount',
        'duration_months',
        'bio_ar',
        'bio_en',
        'photo',
        'status',
        'waiting_since',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'monthly_amount' => 'decimal:2',
            'duration_months' => 'integer',
            'waiting_since' => 'date',
        ];
    }

    /**
     * Cases visible to the public (available or already sponsored).
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_AVAILABLE, self::STATUS_SPONSORED]);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Longest-waiting first.
     */
    public function scopeLongestWaiting(Builder $query): Builder
    {
        return $query->orderByRaw('waiting_since is null')->orderBy('waiting_since')->orderBy('id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function getNameAttribute(): string
    {
        return (app()->getLocale() === 'en' && ! empty($this->name_en)) ? $this->name_en : $this->name_ar;
    }

    public function getBioAttribute(): ?string
    {
        return (app()->getLocale() === 'en' && ! empty($this->bio_en)) ? $this->bio_en : $this->bio_ar;
    }

    public function getYearlyAmountAttribute(): float
    {
        return (float) $this->monthly_amount * 12;
    }

    public function getTypeLabelAttribute(): string
    {
        return __('sponsorship.type.'.$this->type);
    }
}
