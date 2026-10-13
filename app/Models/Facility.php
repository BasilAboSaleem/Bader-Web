<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'region_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'content_ar',
        'content_en',
        'location_ar',
        'location_en',
        'image',
        'gallery',
        'video_url',
        'established_year',
        'capacity_ar',
        'capacity_en',
        'order',
        'status',
        'show_on_home',
    ];

    public const HOME_LIMIT = 4;

    protected $casts = [
        'gallery' => 'array',
        'show_on_home' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->orderBy('order');
    }

    /**
     * Published facilities picked for the home page, falling back to the first
     * published ones until the team picks any.
     *
     * @return Collection<int, Facility>
     */
    public static function forHome(): Collection
    {
        $picked = static::published()->where('show_on_home', true)->take(self::HOME_LIMIT)->get();

        return $picked->isNotEmpty() ? $picked : static::published()->take(self::HOME_LIMIT)->get();
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function getNameAttribute(): string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->name_en)) ? $this->name_en : $this->name_ar;
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

    public function getLocationAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->location_en)) ? $this->location_en : $this->location_ar;
    }

    public function getCapacityAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->capacity_en)) ? $this->capacity_en : $this->capacity_ar;
    }
}
