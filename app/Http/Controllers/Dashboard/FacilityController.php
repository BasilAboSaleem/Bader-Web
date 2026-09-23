<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(): View
    {
        $facilities = Facility::query()->orderBy('order')->paginate(15);

        return view('dashboard.facilities.index', compact('facilities'));
    }

    public function create(): View
    {
        return view('dashboard.facilities.form', [
            'facility' => new Facility,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:facilities,key'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'location_ar' => ['nullable', 'string', 'max:255'],
            'location_en' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
            'status' => ['required', 'in:draft,under_review,published'],
        ]);

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['name_en'] ?? $validated['name_ar']) ?: 'facility-'.time();
        }

        Facility::create($validated);

        return redirect()->route('dashboard.facilities.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(Facility $facility): View
    {
        return view('dashboard.facilities.form', [
            'facility' => $facility,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:facilities,key,'.$facility->id],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'location_ar' => ['nullable', 'string', 'max:255'],
            'location_en' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
            'status' => ['required', 'in:draft,under_review,published'],
        ]);

        $facility->update($validated);

        return redirect()->route('dashboard.facilities.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(Facility $facility): RedirectResponse
    {
        $facility->delete();

        return redirect()->route('dashboard.facilities.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }
}
