<?php

namespace App\Models;

use App\Models\Concerns\HasMediaGallery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory, HasMediaGallery;

    protected $fillable = [
        'key',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'image',
        'category_ar',
        'category_en',
        'badge_ar',
        'badge_en',
        'highlight_ar',
        'highlight_en',
        'is_flagship',
        'icon',
        'order',
        'status',
    ];

    protected $casts = [
        'is_flagship' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->orderBy('order');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function completedProjects(): HasMany
    {
        return $this->hasMany(CompletedProject::class);
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

    public function getCategoryAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->category_en)) ? $this->category_en : $this->category_ar;
    }

    public function getBadgeAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->badge_en)) ? $this->badge_en : $this->badge_ar;
    }

    public function getHighlightAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->highlight_en)) ? $this->highlight_en : $this->highlight_ar;
    }
}
