<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Story;
use App\Support\MediaGalleryInput;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function index(): View
    {
        $stories = Story::query()->latest('published_at')->paginate(15);

        return view('dashboard.stories.index', compact('stories'));
    }

    public function create(): View
    {
        return view('dashboard.stories.form', [
            'story' => new Story([
                'published_at' => now()->toDateString(),
                'status' => 'published',
            ]),
            'programs' => $this->programOptions(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:stories,key'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'excerpt_ar' => ['nullable', 'string'],
            'excerpt_en' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'category_ar' => ['nullable', 'string', 'max:100'],
            'category_en' => ['nullable', 'string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,under_review,published'],
            ...MediaGalleryInput::rules(),
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('stories', 'public');
            $validated['image'] = 'storage/'.$path;
        }

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['title_en'] ?? $validated['title_ar']) ?: 'story-'.time();
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['published_at'] = $validated['published_at'] ?? now()->toDateString();

        unset($validated['image_file'], $validated['gallery_files'], $validated['gallery_remove']);

        Story::create([...$validated, ...MediaGalleryInput::apply($request, [], 'stories/gallery')]);

        return redirect()->route('dashboard.stories.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(Story $story): View
    {
        return view('dashboard.stories.form', [
            'story' => $story,
            'programs' => $this->programOptions(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Story $story): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:stories,key,'.$story->id],
            'program_id' => ['nullable', 'exists:programs,id'],
            'excerpt_ar' => ['nullable', 'string'],
            'excerpt_en' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'category_ar' => ['nullable', 'string', 'max:100'],
            'category_en' => ['nullable', 'string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,under_review,published'],
            ...MediaGalleryInput::rules(),
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('stories', 'public');
            $validated['image'] = 'storage/'.$path;
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        unset($validated['image_file'], $validated['gallery_files'], $validated['gallery_remove']);

        $story->update([...$validated, ...MediaGalleryInput::apply($request, $story->galleryPhotos(), 'stories/gallery')]);

        return redirect()->route('dashboard.stories.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(Story $story): RedirectResponse
    {
        $story->delete();

        return redirect()->route('dashboard.stories.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }

    /**
     * @return Collection<int, Program>
     */
    private function programOptions(): Collection
    {
        return Program::query()->orderBy('order')->get(['id', 'title_ar']);
    }
}
