<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Facility;
use App\Models\FormSubmission;
use App\Models\Program;
use App\Services\StripePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationPaymentController extends Controller
{
    public function __construct(
        protected StripePaymentService $stripeService
    ) {}

    /**
     * Initiate online donation (Stripe Checkout or Bank Transfer notice).
     */
    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target_type' => ['required', 'string', 'in:campaign,program,facility,general'],
            'target_id' => ['nullable', 'integer'],
            'donation_category' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'in:USD'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:50'],
            'is_anonymous' => ['nullable', 'boolean'],
            'payment_method' => ['required', 'string', 'in:stripe,bank_transfer'],
            'reference_number' => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'transfer_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $currencyAr = 'دولار أمريكي';
        $currencyEn = $validated['currency'];

        $campaignId = null;
        $programId = null;
        $facilityId = null;

        if ($validated['target_type'] === 'campaign' && ! empty($validated['target_id'])) {
            $campaignId = Campaign::where('id', $validated['target_id'])->value('id');
        } elseif ($validated['target_type'] === 'program' && ! empty($validated['target_id'])) {
            $programId = Program::where('id', $validated['target_id'])->value('id');
        } elseif ($validated['target_type'] === 'facility' && ! empty($validated['target_id'])) {
            $facilityId = Facility::where('id', $validated['target_id'])->value('id');
        }

        $donation = Donation::create([
            'donor_name' => $validated['donor_name'] ?? null,
            'donor_email' => $validated['donor_email'] ?? null,
            'donor_phone' => $validated['donor_phone'] ?? null,
            'is_anonymous' => (bool) ($validated['is_anonymous'] ?? false),
            'target_type' => $validated['target_type'],
            'campaign_id' => $campaignId,
            'program_id' => $programId,
            'facility_id' => $facilityId,
            'donation_category' => $validated['donation_category'],
            'amount' => $validated['amount'],
            'currency_ar' => $currencyAr,
            'currency_en' => $currencyEn,
            'payment_method' => $validated['payment_method'],
            'payment_gateway' => $validated['payment_method'] === 'stripe' ? 'stripe' : 'manual_transfer',
            'reference_number' => $validated['reference_number'] ?? null,
            'transfer_date' => $validated['transfer_date'] ?? now()->toDateString(),
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['payment_method'] === 'stripe' ? 'pending' : 'pending',
        ]);

        if ($validated['payment_method'] === 'stripe') {
            $session = $this->stripeService->createCheckoutSession(
                $donation,
                route('donate.success'),
                route('donate.cancel')
            );

            $donation->update(['stripe_session_id' => $session['id']]);

            return redirect()->away($session['url']);
        }

        // Bank Transfer Submission Notification to Inbox
        FormSubmission::create([
            'type' => 'donation_transfer',
            'name' => $donation->display_name,
            'email' => $donation->donor_email,
            'phone' => $donation->donor_phone,
            'subject' => __('donation.transfer_notice_subject'),
            'message' => __('donation.transfer_notice_message', [
                'amount' => $donation->amount.' '.$donation->currency_ar,
                'ref' => $donation->reference_number,
            ]),
            'payload' => [
                'donation_id' => $donation->id,
                'receipt_number' => $donation->receipt_number,
                'target_type' => $donation->target_type,
            ],
            'status' => 'unread',
        ]);

        return redirect()->route('donate.success', ['receipt' => $donation->receipt_number]);
    }

    /**
     * Donation Success Return Page.
     */
    public function success(Request $request): View
    {
        $sessionId = $request->query('session_id');
        $receiptNumber = $request->query('receipt');

        $donation = null;

        if ($sessionId) {
            $donation = $this->stripeService->handleSessionCompletion($sessionId);
        }

        if (! $donation && $receiptNumber) {
            $donation = Donation::where('receipt_number', $receiptNumber)->first();
        }

        if (! $donation && $sessionId) {
            $donation = Donation::where('stripe_session_id', $sessionId)->first();
        }

        return view('pages.donate-success', [
            'donation' => $donation,
        ]);
    }

    /**
     * Donation Cancellation Return Page.
     */
    public function cancel(): RedirectResponse
    {
        return redirect()->route('donate')->with('warning_message', __('donation.cancelled_message'));
    }

    /**
     * Handle Stripe Webhook Events.
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();

        if (isset($payload['type']) && $payload['type'] === 'checkout.session.completed') {
            $sessionId = $payload['data']['object']['id'] ?? null;
            if ($sessionId) {
                $this->stripeService->handleSessionCompletion($sessionId);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
