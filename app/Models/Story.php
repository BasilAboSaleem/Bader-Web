<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title_ar',
        'title_en',
        'excerpt_ar',
        'excerpt_en',
        'content_ar',
        'content_en',
        'category_ar',
        'category_en',
        'published_at',
        'image',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_featured' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->orderByDesc('published_at');
    }

    public function getTitleAttribute(): string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->title_en)) ? $this->title_en : $this->title_ar;
    }

    public function getExcerptAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->excerpt_en)) ? $this->excerpt_en : $this->excerpt_ar;
    }

    public function getContentAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->content_en)) ? $this->content_en : $this->content_ar;
    }

    public function getCategoryAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->category_en)) ? $this->category_en : $this->category_ar;
    }
}
