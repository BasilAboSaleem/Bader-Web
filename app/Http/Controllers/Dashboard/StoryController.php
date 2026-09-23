<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Story;
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
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:stories,key'],
            'excerpt_ar' => ['nullable', 'string'],
            'excerpt_en' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'category_ar' => ['nullable', 'string', 'max:100'],
            'category_en' => ['nullable', 'string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,under_review,published'],
        ]);

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['title_en'] ?? $validated['title_ar']) ?: 'story-'.time();
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['published_at'] = $validated['published_at'] ?? now()->toDateString();

        Story::create($validated);

        return redirect()->route('dashboard.stories.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(Story $story): View
    {
        return view('dashboard.stories.form', [
            'story' => $story,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Story $story): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:stories,key,'.$story->id],
            'excerpt_ar' => ['nullable', 'string'],
            'excerpt_en' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'category_ar' => ['nullable', 'string', 'max:100'],
            'category_en' => ['nullable', 'string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,under_review,published'],
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');

        $story->update($validated);

        return redirect()->route('dashboard.stories.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(Story $story): RedirectResponse
    {
        $story->delete();

        return redirect()->route('dashboard.stories.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }
}
