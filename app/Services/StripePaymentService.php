<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\SponsorshipCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    /**
     * Whether a real Stripe secret key is configured.
     */
    public function isLive(): bool
    {
        $secretKey = (string) config('services.stripe.secret');

        return $secretKey !== '' && ! str_contains($secretKey, 'dummy');
    }

    /**
     * Card payments are offered when Stripe is configured, or outside production (simulation mode).
     */
    public function isAvailable(): bool
    {
        return $this->isLive() || ! app()->isProduction();
    }

    /**
     * Create a Stripe Checkout Session for a donation (one-time or monthly subscription).
     *
     * @return array{id: string, url: string}
     */
    public function createCheckoutSession(Donation $donation, string $successUrl, string $cancelUrl): array
    {
        $unitAmount = (int) round(((float) $donation->amount) * 100);
        $isMonthly = $donation->isMonthly();

        if ($this->isLive()) {
            $priceData = [
                'currency' => strtolower($donation->currency_en ?: 'USD'),
                'unit_amount' => $unitAmount,
                'product_data' => [
                    'name' => __('donation.stripe_product', ['target' => $donation->target_title]),
                    'description' => __('donation.stripe_description', ['receipt' => $donation->receipt_number]),
                ],
            ];

            if ($isMonthly) {
                $priceData['recurring'] = ['interval' => 'month'];
            }

            try {
                $response = Http::asForm()
                    ->withToken((string) config('services.stripe.secret'))
                    ->post('https://api.stripe.com/v1/checkout/sessions', array_filter([
                        'mode' => $isMonthly ? 'subscription' : 'payment',
                        'customer_email' => $donation->donor_email,
                        'success_url' => $successUrl.'?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => $cancelUrl,
                        'line_items' => [['price_data' => $priceData, 'quantity' => 1]],
                        'metadata' => [
                            'donation_id' => (string) $donation->id,
                            'receipt_number' => $donation->receipt_number,
                            'target_type' => $donation->target_type,
                        ],
                    ]));

                if ($response->successful()) {
                    return [
                        'id' => (string) $response->json('id'),
                        'url' => (string) $response->json('url'),
                    ];
                }

                Log::error('Stripe Checkout Session error: '.$response->body());
            } catch (\Throwable $e) {
                Log::error('Stripe HTTP Exception: '.$e->getMessage());
            }

            abort(502, __('donation.gateway_unavailable'));
        }

        $simulatedSessionId = 'cs_test_'.bin2hex(random_bytes(12));

        return [
            'id' => $simulatedSessionId,
            'url' => $successUrl.'?session_id='.$simulatedSessionId,
        ];
    }

    /**
     * Confirm a Checkout Session after the donor returns, and mark the donation completed when paid.
     */
    public function confirmSession(string $sessionId): ?Donation
    {
        $donation = Donation::where('stripe_session_id', $sessionId)->first();

        if (! $donation) {
            return null;
        }

        if (! $this->isLive()) {
            return app()->isProduction() ? $donation : $this->markCompleted($donation);
        }

        try {
            $response = Http::withToken((string) config('services.stripe.secret'))
                ->get('https://api.stripe.com/v1/checkout/sessions/'.urlencode($sessionId));
        } catch (\Throwable $e) {
            Log::error('Stripe session lookup failed: '.$e->getMessage());

            return $donation;
        }

        if ($response->successful() && $response->json('payment_status') === 'paid') {
            return $this->markCompleted($donation, $response->json());
        }

        return $donation;
    }

    /**
     * Handle a verified `checkout.session.completed` webhook payload.
     *
     * @param  array<string, mixed>  $session
     */
    public function handleCompletedSession(array $session): ?Donation
    {
        $donation = Donation::where('stripe_session_id', $session['id'] ?? null)->first();

        if (! $donation || ($session['payment_status'] ?? null) !== 'paid') {
            return $donation;
        }

        return $this->markCompleted($donation, $session);
    }

    /**
     * Verify the `Stripe-Signature` header against the raw request payload.
     */
    public function hasValidSignature(string $payload, ?string $signatureHeader): bool
    {
        $secret = (string) config('services.stripe.webhook_secret');

        if ($secret === '' || empty($signatureHeader)) {
            return false;
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);

            if ($key === 't') {
                $timestamp = $value;
            } elseif ($key === 'v1' && $value !== null) {
                $signatures[] = $value;
            }
        }

        if (! is_numeric($timestamp) || $signatures === []) {
            return false;
        }

        if (abs(time() - (int) $timestamp) > (int) config('services.stripe.webhook_tolerance', 300)) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $session
     */
    private function markCompleted(Donation $donation, array $session = []): Donation
    {
        if (in_array($donation->status, ['completed', 'verified'], true)) {
            return $donation;
        }

        $donation->update(array_filter([
            'status' => 'completed',
            'verified_at' => now(),
            'stripe_payment_intent_id' => $session['payment_intent'] ?? null,
            'stripe_subscription_id' => $session['subscription'] ?? null,
        ]));

        if ($donation->target_type === 'campaign' && $donation->campaign) {
            $donation->campaign->increment('raised_amount', (float) $donation->amount);
        }

        if ($donation->target_type === 'sponsorship' && $donation->sponsorshipCase?->isAvailable()) {
            $donation->sponsorshipCase->update(['status' => SponsorshipCase::STATUS_SPONSORED]);
        }

        return $donation;
    }
}
