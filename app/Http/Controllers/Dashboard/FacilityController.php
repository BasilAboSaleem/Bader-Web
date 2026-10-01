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
            'content_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'location_ar' => ['nullable', 'string', 'max:255'],
            'location_en' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'established_year' => ['nullable', 'string', 'max:10'],
            'capacity_ar' => ['nullable', 'string', 'max:255'],
            'capacity_en' => ['nullable', 'string', 'max:255'],
            'gallery_files.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'order' => ['nullable', 'integer'],
            'status' => ['required', 'in:draft,under_review,published'],
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('facilities', 'public');
            $validated['image'] = 'storage/'.$path;
        }

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['name_en'] ?? $validated['name_ar']) ?: 'facility-'.time();
        }

        // Build gallery array from paths text + uploaded files
        $galleryPaths = array_filter(array_map('trim', explode(',', $request->input('gallery_paths', ''))));
        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $file) {
                $path = $file->store('facilities/gallery', 'public');
                $galleryPaths[] = 'storage/'.$path;
            }
        }
        $validated['gallery'] = ! empty($galleryPaths) ? array_values($galleryPaths) : null;

        unset($validated['image_file'], $validated['gallery_files']);

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
            'content_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'location_ar' => ['nullable', 'string', 'max:255'],
            'location_en' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'established_year' => ['nullable', 'string', 'max:10'],
            'capacity_ar' => ['nullable', 'string', 'max:255'],
            'capacity_en' => ['nullable', 'string', 'max:255'],
            'gallery_files.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'order' => ['nullable', 'integer'],
            'status' => ['required', 'in:draft,under_review,published'],
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('facilities', 'public');
            $validated['image'] = 'storage/'.$path;
        }

        // Rebuild gallery: start from text paths, append newly uploaded files
        $galleryPaths = array_filter(array_map('trim', explode(',', $request->input('gallery_paths', ''))));
        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $file) {
                $path = $file->store('facilities/gallery', 'public');
                $galleryPaths[] = 'storage/'.$path;
            }
        }
        $validated['gallery'] = ! empty($galleryPaths) ? array_values($galleryPaths) : null;

        unset($validated['image_file'], $validated['gallery_files']);

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
