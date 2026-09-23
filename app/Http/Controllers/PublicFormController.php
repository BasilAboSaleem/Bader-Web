<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicFormController extends Controller
{
    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        FormSubmission::create([
            'type' => 'contact',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'status' => 'unread',
        ]);

        return back()->with('success_message', __('form.contact.success'));
    }

    public function submitPartnership(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'organization' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'partnership_type' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        FormSubmission::create([
            'type' => 'partnership',
            'name' => $validated['name'],
            'organization' => $validated['organization'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'payload' => [
                'partnership_type' => $validated['partnership_type'] ?? null,
            ],
            'status' => 'unread',
        ]);

        return back()->with('success_message', __('form.partnership.success'));
    }

    public function submitVolunteer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:255'],
            'skills' => ['nullable', 'string', 'max:500'],
            'availability' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:3000'],
        ]);

        FormSubmission::create([
            'type' => 'volunteer',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'location' => $validated['location'],
            'message' => $validated['message'] ?? null,
            'payload' => [
                'skills' => $validated['skills'] ?? null,
                'availability' => $validated['availability'] ?? null,
            ],
            'status' => 'unread',
        ]);

        return back()->with('success_message', __('form.volunteer.success'));
    }

    public function submitSponsorship(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'sponsorship_type' => ['required', 'string', 'max:100'],
            'beneficiaries_count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'message' => ['nullable', 'string', 'max:3000'],
        ]);

        FormSubmission::create([
            'type' => 'sponsorship',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'] ?? null,
            'payload' => [
                'sponsorship_type' => $validated['sponsorship_type'],
                'beneficiaries_count' => $validated['beneficiaries_count'] ?? 1,
            ],
            'status' => 'unread',
        ]);

        return back()->with('success_message', __('form.sponsorship.success'));
    }

    public function submitAssistance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:255'],
            'family_members' => ['nullable', 'integer', 'min:1'],
            'need_type' => ['required', 'string', 'max:100'],
            'urgency' => ['nullable', 'string', 'in:normal,urgent,critical'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        FormSubmission::create([
            'type' => 'assistance',
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'location' => $validated['location'],
            'message' => $validated['message'],
            'payload' => [
                'national_id' => $validated['national_id'] ?? null,
                'family_members' => $validated['family_members'] ?? null,
                'need_type' => $validated['need_type'],
                'urgency' => $validated['urgency'] ?? 'normal',
            ],
            'status' => 'unread',
        ]);

        return back()->with('success_message', __('form.assistance.success'));
    }

    public function notifyTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:50'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'reference_number' => ['required', 'string', 'max:100'],
            'transfer_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Donation::create([
            'donor_name' => $validated['donor_name'] ?? __('donation.anonymous'),
            'donor_email' => $validated['donor_email'] ?? null,
            'donor_phone' => $validated['donor_phone'] ?? null,
            'campaign_id' => $validated['campaign_id'] ?? null,
            'amount' => $validated['amount'],
            'payment_method' => 'bank_transfer',
            'reference_number' => $validated['reference_number'],
            'transfer_date' => $validated['transfer_date'] ?? now()->toDateString(),
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Also create a form submission entry so it's surfaced immediately in the inbox
        FormSubmission::create([
            'type' => 'donation_transfer',
            'name' => $validated['donor_name'] ?? __('donation.anonymous'),
            'email' => $validated['donor_email'] ?? null,
            'phone' => $validated['donor_phone'] ?? null,
            'subject' => __('donation.transfer_notice_subject'),
            'message' => __('donation.transfer_notice_message', [
                'amount' => $validated['amount'],
                'ref' => $validated['reference_number'],
            ]),
            'payload' => [
                'amount' => $validated['amount'],
                'reference_number' => $validated['reference_number'],
                'campaign_id' => $validated['campaign_id'] ?? null,
            ],
            'status' => 'unread',
        ]);

        return back()->with('success_message', __('form.transfer.success'));
    }
}
