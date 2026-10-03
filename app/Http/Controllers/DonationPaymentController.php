<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Facility;
use App\Models\FormSubmission;
use App\Models\Program;
use App\Models\SponsorshipCase;
use App\Services\StripePaymentService;
use App\Support\SiteSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DonationPaymentController extends Controller
{
    public function __construct(
        protected StripePaymentService $stripeService
    ) {}

    /**
     * Initiate a donation: Stripe Checkout for cards, or a pending record for bank transfers.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $paymentMethods = $this->stripeService->isAvailable() ? ['stripe', 'bank_transfer'] : ['bank_transfer'];

        $validated = $request->validate([
            'target_type' => ['required', 'string', 'in:campaign,program,facility,sponsorship,general'],
            'target_id' => ['nullable', 'integer'],
            'donation_category' => ['required', 'string', Rule::in(array_keys(SiteSettings::donationCategories()))],
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'frequency' => ['nullable', Rule::in([Donation::FREQUENCY_ONCE, Donation::FREQUENCY_MONTHLY])],
            'currency' => ['nullable', 'string', 'in:USD'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255', 'required_if:payment_method,stripe'],
            'donor_phone' => ['nullable', 'string', 'max:50'],
            'is_anonymous' => ['nullable', 'boolean'],
            'payment_method' => ['required', 'string', Rule::in($paymentMethods)],
            'reference_number' => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'transfer_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_gift' => ['nullable', 'boolean'],
            'gift_recipient_name' => ['required_if_accepted:is_gift', 'nullable', 'string', 'max:255'],
            'gift_recipient_contact' => ['nullable', 'string', 'max:255'],
            'gift_sender_name' => ['nullable', 'string', 'max:255'],
            'gift_message' => ['nullable', 'string', 'max:500'],
            'gift_card_design' => ['nullable', 'string', Rule::in(array_keys(SiteSettings::giftDesigns()))],
        ]);

        $target = $this->resolveTarget($validated['target_type'], $validated['target_id'] ?? null);
        $isStripe = $validated['payment_method'] === 'stripe';
        $isGift = (bool) ($validated['is_gift'] ?? false);

        $donation = Donation::create([
            'donor_name' => $validated['donor_name'] ?? null,
            'donor_email' => $validated['donor_email'] ?? null,
            'donor_phone' => $validated['donor_phone'] ?? null,
            'is_anonymous' => (bool) ($validated['is_anonymous'] ?? false),
            'target_type' => $target['type'],
            'campaign_id' => $target['campaign_id'],
            'program_id' => $target['program_id'],
            'facility_id' => $target['facility_id'],
            'sponsorship_case_id' => $target['sponsorship_case_id'],
            'donation_category' => $validated['donation_category'],
            'amount' => $validated['amount'],
            'frequency' => $validated['frequency'] ?? Donation::FREQUENCY_ONCE,
            'currency_ar' => 'دولار أمريكي',
            'currency_en' => 'USD',
            'payment_method' => $validated['payment_method'],
            'payment_gateway' => $isStripe ? 'stripe' : 'manual_transfer',
            'reference_number' => $validated['reference_number'] ?? null,
            'transfer_date' => $validated['transfer_date'] ?? now()->toDateString(),
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'is_gift' => $isGift,
            'gift_recipient_name' => $isGift ? $validated['gift_recipient_name'] : null,
            'gift_recipient_contact' => $isGift ? ($validated['gift_recipient_contact'] ?? null) : null,
            'gift_sender_name' => $isGift ? ($validated['gift_sender_name'] ?? null) : null,
            'gift_message' => $isGift ? ($validated['gift_message'] ?? null) : null,
            'gift_card_design' => $isGift ? ($validated['gift_card_design'] ?? array_key_first(SiteSettings::giftDesigns())) : null,
        ]);

        if ($isStripe) {
            $session = $this->stripeService->createCheckoutSession(
                $donation,
                route('donate.success'),
                route('donate.cancel')
            );

            $donation->update(['stripe_session_id' => $session['id']]);

            return redirect()->away($session['url']);
        }

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
                'frequency' => $donation->frequency,
            ],
            'status' => 'unread',
        ]);

        return redirect()->route('donate.success', ['receipt' => $donation->receipt_number]);
    }

    /**
     * Donation success return page.
     */
    public function success(Request $request): View
    {
        $sessionId = $request->query('session_id');
        $receiptNumber = $request->query('receipt');

        $donation = null;

        if (is_string($sessionId) && $sessionId !== '') {
            $donation = $this->stripeService->confirmSession($sessionId);
        }

        if (! $donation && is_string($receiptNumber) && $receiptNumber !== '') {
            $donation = Donation::where('receipt_number', $receiptNumber)->first();
        }

        return view('pages.donate-success', ['donation' => $donation]);
    }

    /**
     * Donation cancellation return page.
     */
    public function cancel(): RedirectResponse
    {
        return redirect()->route('donate')->with('warning_message', __('donation.cancelled_message'));
    }

    /**
     * Handle signed Stripe webhook events.
     */
    public function webhook(Request $request): JsonResponse
    {
        if (! $this->stripeService->hasValidSignature($request->getContent(), $request->header('Stripe-Signature'))) {
            return response()->json(['status' => 'invalid_signature'], 400);
        }

        $event = json_decode($request->getContent(), true);

        if (($event['type'] ?? null) === 'checkout.session.completed' && is_array($event['data']['object'] ?? null)) {
            $this->stripeService->handleCompletedSession($event['data']['object']);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * @return array{type: string, campaign_id: ?int, program_id: ?int, facility_id: ?int, sponsorship_case_id: ?int}
     */
    private function resolveTarget(string $type, ?int $targetId): array
    {
        $target = ['type' => 'general', 'campaign_id' => null, 'program_id' => null, 'facility_id' => null, 'sponsorship_case_id' => null];

        if ($targetId === null) {
            return $target;
        }

        [$column, $id] = match ($type) {
            'campaign' => ['campaign_id', Campaign::published()->whereKey($targetId)->value('id')],
            'program' => ['program_id', Program::where('status', 'published')->whereKey($targetId)->value('id')],
            'facility' => ['facility_id', Facility::where('status', 'published')->whereKey($targetId)->value('id')],
            'sponsorship' => ['sponsorship_case_id', SponsorshipCase::available()->whereKey($targetId)->value('id')],
            default => [null, null],
        };

        if ($id === null) {
            return $target;
        }

        $target['type'] = $type;
        $target[$column] = $id;

        return $target;
    }
}
