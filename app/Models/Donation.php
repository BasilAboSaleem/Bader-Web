<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Donation extends Model
{
    use HasFactory;

    public const FREQUENCY_ONCE = 'once';

    public const FREQUENCY_MONTHLY = 'monthly';

    protected $fillable = [
        'receipt_number',
        'donor_name',
        'donor_email',
        'donor_phone',
        'is_anonymous',
        'target_type',
        'campaign_id',
        'program_id',
        'facility_id',
        'sponsorship_case_id',
        'donation_category',
        'amount',
        'frequency',
        'currency_ar',
        'currency_en',
        'payment_method',
        'payment_gateway',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_subscription_id',
        'reference_number',
        'transfer_date',
        'receipt_path',
        'notes',
        'status',
        'verified_at',
        'is_gift',
        'gift_recipient_name',
        'gift_recipient_contact',
        'gift_sender_name',
        'gift_message',
        'gift_card_design',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_anonymous' => 'boolean',
            'is_gift' => 'boolean',
            'transfer_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Donation $donation): void {
            if (empty($donation->receipt_number)) {
                $donation->receipt_number = 'BDR-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
            }
        });
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function sponsorshipCase(): BelongsTo
    {
        return $this->belongsTo(SponsorshipCase::class);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    /**
     * Donations that represent money actually received (online or verified manually).
     */
    public function scopeSettled(Builder $query): Builder
    {
        return $query->whereIn('status', ['verified', 'completed']);
    }

    public function isMonthly(): bool
    {
        return $this->frequency === self::FREQUENCY_MONTHLY;
    }

    /**
     * @return array{key: string, label_ar: string, label_en: string, accent: string}|null
     */
    public function giftCard(): ?array
    {
        if (! $this->is_gift) {
            return null;
        }

        $designs = config('bader.gift_designs', []);

        return $designs[$this->gift_card_design] ?? reset($designs) ?: null;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous || empty($this->donor_name)) {
            return __('donation.anonymous');
        }

        return $this->donor_name;
    }

    public function getTargetTitleAttribute(): string
    {
        return match ($this->target_type) {
            'campaign' => $this->campaign?->title,
            'program' => $this->program?->title,
            'facility' => $this->facility?->name,
            'sponsorship' => $this->sponsorshipCase
                ? __('sponsorship.donation_target', ['name' => $this->sponsorshipCase->name, 'code' => $this->sponsorshipCase->code])
                : null,
            default => null,
        } ?? __('donation.target_general');
    }
}
