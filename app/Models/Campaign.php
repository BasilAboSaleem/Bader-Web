<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    /** @var list<int> */
    public const DEFAULT_PRESET_AMOUNTS = [10, 25, 50];

    protected $fillable = [
        'key',
        'program_id',
        'region_id',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'content_ar',
        'content_en',
        'goal_amount',
        'raised_amount',
        'preset_amounts',
        'allows_monthly',
        'currency_ar',
        'currency_en',
        'image',
        'is_featured',
        'order',
        'status',
    ];

    protected $casts = [
        'goal_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
        'preset_amounts' => 'array',
        'allows_monthly' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('is_featured', true);
    }

    /**
     * Ordered for public listings: featured first, then manual order, newest last.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('order')->orderByDesc('id');
    }

    /**
     * Count donations that were actually received (completed online or verified manually).
     */
    public function scopeWithDonorsCount(Builder $query): Builder
    {
        return $query->withCount(['donations as donors_count' => fn (Builder $donations) => $donations->settled()]);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function getTitleAttribute(): string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->title_en)) ? $this->title_en : $this->title_ar;
    }

    public function getDescriptionAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->description_en)) ? $this->description_en : $this->description_ar;
    }

    public function getContentAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->content_en)) ? $this->content_en : $this->content_ar;
    }

    public function getCurrencyAttribute(): string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->currency_en)) ? $this->currency_en : $this->currency_ar;
    }

    /**
     * Ongoing projects have no fundraising target ("continuous giving").
     */
    public function isOngoing(): bool
    {
        return (float) $this->goal_amount <= 0;
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->isOngoing()) {
            return 0;
        }

        return (int) min(100, floor(((float) $this->raised_amount / (float) $this->goal_amount) * 100));
    }

    /**
     * @return list<int|float>
     */
    public function getAmountOptionsAttribute(): array
    {
        $amounts = array_values(array_filter(
            array_map(fn ($amount) => is_numeric($amount) ? $amount + 0 : null, $this->preset_amounts ?? []),
            fn ($amount) => $amount !== null && $amount > 0,
        ));

        return $amounts !== [] ? $amounts : self::DEFAULT_PRESET_AMOUNTS;
    }
}
