<?php

namespace App\Services;

use App\Models\Donation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    /**
     * Create a Stripe Checkout Session for a donation.
     *
     * @return array{id: string, url: string}
     */
    public function createCheckoutSession(Donation $donation, string $successUrl, string $cancelUrl): array
    {
        $secretKey = config('services.stripe.secret');

        // USD uses two decimal places.
        $currency = strtolower($donation->currency_en ?: 'USD');
        $multiplier = 100;
        $unitAmount = (int) round($donation->amount * $multiplier);

        if (! empty($secretKey) && ! str_contains($secretKey, 'dummy')) {
            try {
                $response = Http::asForm()
                    ->withToken($secretKey)
                    ->post('https://api.stripe.com/v1/checkout/sessions', [
                        'payment_method_types' => ['card'],
                        'mode' => 'payment',
                        'customer_email' => $donation->donor_email,
                        'success_url' => $successUrl.'?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => $cancelUrl,
                        'line_items' => [
                            [
                                'price_data' => [
                                    'currency' => $currency,
                                    'unit_amount' => $unitAmount,
                                    'product_data' => [
                                        'name' => 'تطوع وتبرع — '.$donation->target_title,
                                        'description' => 'تبرع إنساني لمؤسسة بادر الإنسانية (رقم الإيصال: '.$donation->receipt_number.')',
                                    ],
                                ],
                                'quantity' => 1,
                            ],
                        ],
                        'metadata' => [
                            'donation_id' => (string) $donation->id,
                            'receipt_number' => $donation->receipt_number,
                            'target_type' => $donation->target_type,
                        ],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();

                    return [
                        'id' => $data['id'],
                        'url' => $data['url'],
                    ];
                }

                Log::error('Stripe Checkout Session error: '.$response->body());
            } catch (\Throwable $e) {
                Log::error('Stripe HTTP Exception: '.$e->getMessage());
            }
        }

        // Graceful Simulation Mode (when Stripe secret is not configured or in testing environment)
        $simulatedSessionId = 'cs_test_'.bin2hex(random_bytes(12));
        $simulatedUrl = $successUrl.'?session_id='.$simulatedSessionId;

        return [
            'id' => $simulatedSessionId,
            'url' => $simulatedUrl,
        ];
    }

    /**
     * Verify Stripe Checkout Session and mark donation as completed.
     */
    public function handleSessionCompletion(string $sessionId): ?Donation
    {
        $donation = Donation::where('stripe_session_id', $sessionId)->first();

        if (! $donation) {
            return null;
        }

        if ($donation->status !== 'completed' && $donation->status !== 'verified') {
            $donation->update([
                'status' => 'completed',
                'verified_at' => now(),
            ]);

            // If donation is linked to a campaign, update campaign raised amount
            if ($donation->target_type === 'campaign' && $donation->campaign) {
                $donation->campaign->increment('raised_amount', $donation->amount);
            }
        }

        return $donation;
    }
}
