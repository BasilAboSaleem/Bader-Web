<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ImpactMetric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ImpactMetricController extends Controller
{
    public function index(): View
    {
        $metrics = ImpactMetric::query()->orderBy('order')->latest()->paginate(20);

        return view('dashboard.impact.index', compact('metrics'));
    }

    public function create(): View
    {
        return view('dashboard.impact.form', [
            'metric' => new ImpactMetric(['is_approved' => false, 'status' => 'draft']),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:impact_metrics,key'],
            'value' => ['required', 'string', 'max:100'],
            'unit_ar' => ['nullable', 'string', 'max:100'],
            'unit_en' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'integer'],
            'is_approved' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,approved,archived'],
        ]);

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['title_en'] ?? $validated['title_ar']) ?: 'metric-'.time();
        }

        $validated['is_approved'] = $request->boolean('is_approved');
        if ($validated['is_approved'] && $validated['status'] === 'approved') {
            $validated['approved_at'] = now();
        }

        ImpactMetric::create($validated);

        return redirect()->route('dashboard.impact.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(ImpactMetric $impact): View
    {
        return view('dashboard.impact.form', [
            'metric' => $impact,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, ImpactMetric $impact): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:impact_metrics,key,'.$impact->id],
            'value' => ['required', 'string', 'max:100'],
            'unit_ar' => ['nullable', 'string', 'max:100'],
            'unit_en' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'integer'],
            'is_approved' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,approved,archived'],
        ]);

        $validated['is_approved'] = $request->boolean('is_approved');
        if ($validated['is_approved'] && ! $impact->approved_at) {
            $validated['approved_at'] = now();
        } elseif (! $validated['is_approved']) {
            $validated['approved_at'] = null;
        }

        $impact->update($validated);

        return redirect()->route('dashboard.impact.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function toggleApproval(ImpactMetric $impact): RedirectResponse
    {
        $newApproval = ! $impact->is_approved;
        $impact->update([
            'is_approved' => $newApproval,
            'status' => $newApproval ? 'approved' : 'draft',
            'approved_at' => $newApproval ? now() : null,
        ]);

        return redirect()->route('dashboard.impact.index')
            ->with('status', $newApproval ? __('dashboard.metric_approved') : __('dashboard.metric_unapproved'));
    }

    public function destroy(ImpactMetric $impact): RedirectResponse
    {
        $impact->delete();

        return redirect()->route('dashboard.impact.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }
}
