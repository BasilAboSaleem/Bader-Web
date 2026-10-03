<?php

namespace App\Models;

use App\Support\SiteSettings;
use Database\Factories\FaqFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    /** @use HasFactory<FaqFactory> */
    use HasFactory;

    protected $fillable = [
        'question_ar',
        'question_en',
        'answer_ar',
        'answer_en',
        'order',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->orderBy('order')->orderBy('id');
    }

    public function getQuestionAttribute(): string
    {
        return SiteSettings::withFoundedYear((app()->getLocale() === 'en' && ! empty($this->question_en)) ? $this->question_en : $this->question_ar);
    }

    public function getAnswerAttribute(): string
    {
        return SiteSettings::withFoundedYear((app()->getLocale() === 'en' && ! empty($this->answer_en)) ? $this->answer_en : $this->answer_ar);
    }
}
