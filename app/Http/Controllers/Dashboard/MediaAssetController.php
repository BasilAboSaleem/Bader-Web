<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaAssetController extends Controller
{
    public function index(): View
    {
        $assets = MediaAsset::query()->latest()->paginate(20);

        return view('dashboard.media.index', compact('assets'));
    }

    public function create(): View
    {
        return view('dashboard.media.form', [
            'asset' => new MediaAsset(['has_usage_consent' => true]),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'file_path' => ['required', 'string', 'max:500'],
            'category' => ['nullable', 'string', 'max:100'],
            'has_usage_consent' => ['required', 'boolean'],
            'consent_notes' => ['nullable', 'string'],
        ]);

        $validated['has_usage_consent'] = $request->boolean('has_usage_consent');

        MediaAsset::create($validated);

        return redirect()->route('dashboard.media.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(MediaAsset $medium): View
    {
        return view('dashboard.media.form', [
            'asset' => $medium,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, MediaAsset $medium): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'file_path' => ['required', 'string', 'max:500'],
            'category' => ['nullable', 'string', 'max:100'],
            'has_usage_consent' => ['required', 'boolean'],
            'consent_notes' => ['nullable', 'string'],
        ]);

        $validated['has_usage_consent'] = $request->boolean('has_usage_consent');

        $medium->update($validated);

        return redirect()->route('dashboard.media.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(MediaAsset $medium): RedirectResponse
    {
        $medium->delete();

        return redirect()->route('dashboard.media.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }
}
