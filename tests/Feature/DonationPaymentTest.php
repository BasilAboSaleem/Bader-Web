<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\FormSubmission;
use App\Models\SponsorshipCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DonationPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_donation_redirects_to_checkout_and_completes_in_simulation_mode(): void
    {
        $response = $this->post(route('donate.checkout'), [
            'target_type' => 'general',
            'amount' => 100,
            'donation_category' => 'sadaqah',
            'donor_name' => 'John Doe',
            'donor_email' => 'john@example.com',
            'payment_method' => 'stripe',
        ]);

        $donation = Donation::sole();
        $this->assertSame('general', $donation->target_type);
        $this->assertSame('sadaqah', $donation->donation_category);
        $this->assertSame(Donation::FREQUENCY_ONCE, $donation->frequency);
        $this->assertStringStartsWith('BDR-', $donation->receipt_number);
        $response->assertRedirect(route('donate.success', ['session_id' => $donation->stripe_session_id]));

        $this->get(route('donate.success', ['session_id' => $donation->stripe_session_id]))
            ->assertOk()
            ->assertSee($donation->receipt_number);

        $this->assertSame('completed', $donation->fresh()->status);
    }

    public function test_bank_transfer_donation_to_campaign_is_recorded_as_pending_and_sent_to_inbox(): void
    {
        $campaign = Campaign::factory()->create();

        $response = $this->post(route('donate.checkout'), [
            'target_type' => 'campaign',
            'target_id' => $campaign->id,
            'amount' => 40,
            'frequency' => 'monthly',
            'donation_category' => 'general',
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TRX-5512',
        ]);

        $donation = Donation::sole();
        $response->assertRedirect(route('donate.success', ['receipt' => $donation->receipt_number]));
        $this->assertSame('pending', $donation->status);
        $this->assertSame($campaign->id, $donation->campaign_id);
        $this->assertSame(Donation::FREQUENCY_MONTHLY, $donation->frequency);
        $this->assertSame(2500.0, (float) $campaign->fresh()->raised_amount);
        $this->assertSame($donation->id, FormSubmission::sole()->payload['donation_id']);
    }

    public function test_donation_to_unpublished_campaign_falls_back_to_general(): void
    {
        $campaign = Campaign::factory()->draft()->create();

        $this->post(route('donate.checkout'), [
            'target_type' => 'campaign',
            'target_id' => $campaign->id,
            'amount' => 15,
            'donation_category' => 'general',
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TRX-1',
        ])->assertRedirect();

        $donation = Donation::sole();
        $this->assertSame('general', $donation->target_type);
        $this->assertNull($donation->campaign_id);
    }

    public function test_completed_sponsorship_donation_marks_the_case_as_sponsored(): void
    {
        $case = SponsorshipCase::factory()->create();

        $this->post(route('donate.checkout'), [
            'target_type' => 'sponsorship',
            'target_id' => $case->id,
            'amount' => 40,
            'frequency' => 'monthly',
            'donation_category' => 'orphans',
            'donor_email' => 'sponsor@example.com',
            'payment_method' => 'stripe',
        ]);

        $donation = Donation::sole();
        $this->assertSame($case->id, $donation->sponsorship_case_id);

        $this->get(route('donate.success', ['session_id' => $donation->stripe_session_id]))->assertOk();

        $this->assertSame(SponsorshipCase::STATUS_SPONSORED, $case->fresh()->status);
    }

    public function test_gift_donation_requires_a_recipient_and_stores_the_card_details(): void
    {
        $giftDonation = [
            'target_type' => 'general',
            'amount' => 25,
            'donation_category' => 'sadaqah',
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TRX-77',
            'is_gift' => '1',
            'gift_card_design' => 'eid_fitr',
            'gift_message' => 'Eid Mubarak',
        ];

        $this->post(route('donate.checkout'), $giftDonation)->assertSessionHasErrors('gift_recipient_name');

        $this->post(route('donate.checkout'), $giftDonation + ['gift_recipient_name' => 'Mariam'])->assertRedirect();

        $donation = Donation::sole();
        $this->assertTrue($donation->is_gift);
        $this->assertSame('Mariam', $donation->gift_recipient_name);
        $this->assertSame('eid_fitr', $donation->giftCard()['key']);
    }

    public function test_bank_transfer_requires_a_reference_number(): void
    {
        $this->post(route('donate.checkout'), [
            'target_type' => 'general',
            'amount' => 15,
            'donation_category' => 'general',
            'payment_method' => 'bank_transfer',
        ])->assertSessionHasErrors('reference_number');

        $this->assertDatabaseCount('donations', 0);
    }

    public function test_unpaid_live_session_does_not_complete_the_donation(): void
    {
        config(['services.stripe.secret' => 'sk_test_live_key']);
        Http::fake(['api.stripe.com/*' => Http::response(['payment_status' => 'unpaid'])]);
        $donation = Donation::create([
            'amount' => 50,
            'payment_method' => 'stripe',
            'stripe_session_id' => 'cs_live_unpaid',
            'status' => 'pending',
        ]);

        $this->get(route('donate.success', ['session_id' => 'cs_live_unpaid']))->assertOk();

        $this->assertSame('pending', $donation->fresh()->status);
    }

    public function test_webhook_with_invalid_signature_is_rejected(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test']);
        $donation = Donation::create([
            'amount' => 50,
            'payment_method' => 'stripe',
            'stripe_session_id' => 'cs_signed',
            'status' => 'pending',
        ]);

        $this->call('POST', route('donate.webhook'), [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => 't='.time().',v1=forged',
            'CONTENT_TYPE' => 'application/json',
        ], $this->completedSessionPayload('cs_signed'))->assertStatus(400);

        $this->assertSame('pending', $donation->fresh()->status);
    }

    public function test_signed_webhook_completes_the_donation_and_updates_the_campaign_total(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test']);
        $campaign = Campaign::factory()->create(['raised_amount' => 100]);
        $donation = Donation::create([
            'amount' => 50,
            'target_type' => 'campaign',
            'campaign_id' => $campaign->id,
            'payment_method' => 'stripe',
            'stripe_session_id' => 'cs_signed',
            'status' => 'pending',
        ]);
        $payload = $this->completedSessionPayload('cs_signed');
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_test');

        $this->call('POST', route('donate.webhook'), [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
            'CONTENT_TYPE' => 'application/json',
        ], $payload)->assertOk();

        $this->assertSame('completed', $donation->fresh()->status);
        $this->assertSame(150.0, (float) $campaign->fresh()->raised_amount);
    }

    private function completedSessionPayload(string $sessionId): string
    {
        return json_encode([
            'type' => 'checkout.session.completed',
            'data' => ['object' => ['id' => $sessionId, 'payment_status' => 'paid', 'payment_intent' => 'pi_123']],
        ]);
    }
}
