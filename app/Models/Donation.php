<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_name',
        'donor_email',
        'donor_phone',
        'campaign_id',
        'amount',
        'currency_ar',
        'currency_en',
        'payment_method',
        'reference_number',
        'transfer_date',
        'receipt_path',
        'notes',
        'status',
        'verified_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transfer_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }
}
