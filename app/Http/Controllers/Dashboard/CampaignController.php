<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::query()->latest()->paginate(15);

        return view('dashboard.campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        return view('dashboard.campaigns.form', [
            'campaign' => new Campaign([
                'currency_ar' => 'ريال عماني',
                'currency_en' => 'OMR',
                'status' => 'published',
            ]),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:campaigns,key'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'goal_amount' => ['nullable', 'numeric', 'min:0'],
            'raised_amount' => ['nullable', 'numeric', 'min:0'],
            'currency_ar' => ['nullable', 'string', 'max:50'],
            'currency_en' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,under_review,published'],
        ]);

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['title_en'] ?? $validated['title_ar']) ?: 'campaign-'.time();
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        Campaign::create($validated);

        return redirect()->route('dashboard.campaigns.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(Campaign $campaign): View
    {
        return view('dashboard.campaigns.form', [
            'campaign' => $campaign,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:campaigns,key,'.$campaign->id],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'goal_amount' => ['nullable', 'numeric', 'min:0'],
            'raised_amount' => ['nullable', 'numeric', 'min:0'],
            'currency_ar' => ['nullable', 'string', 'max:50'],
            'currency_en' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,under_review,published'],
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');

        $campaign->update($validated);

        return redirect()->route('dashboard.campaigns.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return redirect()->route('dashboard.campaigns.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }
}
