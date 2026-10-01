<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function index(): View
    {
        $regions = Region::query()
            ->withCount(['campaigns', 'facilities', 'sponsorshipCases'])
            ->orderBy('order')
            ->paginate(15);

        return view('dashboard.regions.index', compact('regions'));
    }

    public function create(): View
    {
        return view('dashboard.regions.form', [
            'region' => new Region(['map_x' => 50, 'map_y' => 50, 'status' => 'published']),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Region::create($this->validatedRegion($request));

        return redirect()->route('dashboard.regions.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(Region $region): View
    {
        return view('dashboard.regions.form', [
            'region' => $region,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Region $region): RedirectResponse
    {
        $region->update($this->validatedRegion($request, $region));

        return redirect()->route('dashboard.regions.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(Region $region): RedirectResponse
    {
        $region->delete();

        return redirect()->route('dashboard.regions.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedRegion(Request $request, ?Region $region = null): array
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'alpha_dash', Rule::unique('regions', 'key')->ignore($region)],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'map_x' => ['required', 'integer', 'between:0,100'],
            'map_y' => ['required', 'integer', 'between:0,100'],
            'order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,published'],
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = 'storage/'.$request->file('image_file')->store('regions', 'public');
        }

        if (empty($validated['key'])) {
            $validated['key'] = $region?->key ?? (Str::slug($validated['name_en'] ?? '') ?: 'region-'.Str::lower(Str::random(6)));
        }

        $validated['order'] ??= 0;
        unset($validated['image_file']);

        return $validated;
    }
}
