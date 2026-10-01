<?php

namespace App\Models;

use Database\Factories\RegionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    /** @use HasFactory<RegionFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image',
        'map_x',
        'map_y',
        'order',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'map_x' => 'integer',
            'map_y' => 'integer',
            'order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->orderBy('order');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    public function sponsorshipCases(): HasMany
    {
        return $this->hasMany(SponsorshipCase::class);
    }

    public function getNameAttribute(): string
    {
        return (app()->getLocale() === 'en' && ! empty($this->name_en)) ? $this->name_en : $this->name_ar;
    }

    public function getDescriptionAttribute(): ?string
    {
        return (app()->getLocale() === 'en' && ! empty($this->description_en)) ? $this->description_en : $this->description_ar;
    }
}
