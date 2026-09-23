<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $campaignId = $request->query('campaign_id');

        $query = Donation::with('campaign')->latest('transfer_date');

        if ($status && in_array($status, ['pending', 'verified', 'rejected'], true)) {
            $query->where('status', $status);
        }

        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }

        $donations = $query->paginate(20)->withQueryString();
        $campaigns = Campaign::all();

        $totalVerified = Donation::where('status', 'verified')->sum('amount');
        $pendingCount = Donation::where('status', 'pending')->count();

        return view('dashboard.donations.index', compact('donations', 'campaigns', 'status', 'campaignId', 'totalVerified', 'pendingCount'));
    }

    public function create(): View
    {
        return view('dashboard.donations.form', [
            'donation' => new Donation([
                'currency_ar' => 'ريال عماني',
                'currency_en' => 'OMR',
                'payment_method' => 'bank_transfer',
                'status' => 'verified',
                'transfer_date' => now()->toDateString(),
            ]),
            'campaigns' => Campaign::all(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:50'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency_ar' => ['nullable', 'string', 'max:50'],
            'currency_en' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'in:bank_transfer,cash,cheque,other'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'transfer_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:pending,verified,rejected'],
        ]);

        if ($validated['status'] === 'verified') {
            $validated['verified_at'] = now();
        }

        $donation = Donation::create($validated);

        // Sync campaign raised amount if verified
        if ($donation->status === 'verified' && $donation->campaign_id) {
            $campaign = Campaign::find($donation->campaign_id);
            if ($campaign) {
                $campaign->increment('raised_amount', (float) $donation->amount);
            }
        }

        return redirect()->route('dashboard.donations.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(Donation $donation): View
    {
        return view('dashboard.donations.form', [
            'donation' => $donation,
            'campaigns' => Campaign::all(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Donation $donation): RedirectResponse
    {
        $validated = $request->validate([
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:50'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency_ar' => ['nullable', 'string', 'max:50'],
            'currency_en' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'in:bank_transfer,cash,cheque,other'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'transfer_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:pending,verified,rejected'],
        ]);

        if ($validated['status'] === 'verified' && ! $donation->verified_at) {
            $validated['verified_at'] = now();
        } elseif ($validated['status'] !== 'verified') {
            $validated['verified_at'] = null;
        }

        $donation->update($validated);

        return redirect()->route('dashboard.donations.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function verify(Donation $donation): RedirectResponse
    {
        $donation->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        if ($donation->campaign_id) {
            $campaign = Campaign::find($donation->campaign_id);
            if ($campaign) {
                $campaign->increment('raised_amount', (float) $donation->amount);
            }
        }

        return redirect()->route('dashboard.donations.index')
            ->with('status', __('dashboard.donation_verified'));
    }

    public function destroy(Donation $donation): RedirectResponse
    {
        $donation->delete();

        return redirect()->route('dashboard.donations.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }
}
