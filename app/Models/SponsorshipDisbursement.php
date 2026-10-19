<?php

namespace App\Models;

use Database\Factories\SponsorshipDisbursementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One monthly payout recorded by finance for a sponsorship. Sponsors see these as proof that their case was paid for that month.
 */
class SponsorshipDisbursement extends Model
{
    /** @use HasFactory<SponsorshipDisbursementFactory> */
    use HasFactory;

    protected $fillable = [
        'sponsorship_id',
        'period_year',
        'period_month',
        'amount',
        'currency',
        'usd_rate',
        'paid_on',
        'notes',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'usd_rate' => 'decimal:6',
            'paid_on' => 'date',
        ];
    }

    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(Sponsorship::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function periodLabel(): string
    {
        return sprintf('%04d-%02d', $this->period_year, $this->period_month);
    }
}
