<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'goal_amount',
        'raised_amount',
        'currency_ar',
        'currency_en',
        'image',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'goal_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('is_featured', true);
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

    public function getCurrencyAttribute(): string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->currency_en)) ? $this->currency_en : $this->currency_ar;
    }
}
