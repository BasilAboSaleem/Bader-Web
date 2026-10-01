<?php

namespace Tests\Feature;

use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_checkout_general_donation()
    {
        $response = $this->post(route('donate.checkout'), [
            'target_type' => 'general',
            'amount' => 100,
            'donation_category' => 'sadaqah',
            'donor_name' => 'John Doe',
            'donor_email' => 'john@example.com',
            'currency' => 'USD',
            'payment_method' => 'stripe',
        ]);

        // It should redirect to success (Simulation mode is on by default if stripe key is not set)
        $response->assertRedirect();

        $donation = Donation::first();
        $this->assertNotNull($donation);
        $this->assertEquals(100, $donation->amount);
        $this->assertEquals('general', $donation->target_type);
        $this->assertEquals('sadaqah', $donation->donation_category);
        $this->assertEquals('John Doe', $donation->donor_name);

        // Assert we got redirected to stripe URL (or success page in simulation)
        $this->assertTrue(str_contains($response->headers->get('Location'), route('donate.success', ['session_id' => 'cs_test_'])) || str_contains($response->headers->get('Location'), 'checkout.stripe.com'));
    }
}
