<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompletedProject;
use App\Models\Program;
use App\Models\Region;
use App\Support\MediaGalleryInput;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompletedProjectController extends Controller
{
    public function index(): View
    {
        $completedProjects = CompletedProject::query()
            ->with(['program', 'region'])
            ->orderByRaw('completed_at is null')
            ->latest('completed_at')
            ->latest('id')
            ->paginate(15);

        return view('dashboard.completed-projects.index', compact('completedProjects'));
    }

    public function create(): View
    {
        return view('dashboard.completed-projects.form', [
            'completedProject' => new CompletedProject(['status' => 'published']),
            ...$this->formOptions(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        CompletedProject::create($this->validatedProject($request));

        return redirect()->route('dashboard.completed-projects.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(CompletedProject $completedProject): View
    {
        return view('dashboard.completed-projects.form', [
            'completedProject' => $completedProject,
            ...$this->formOptions(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, CompletedProject $completedProject): RedirectResponse
    {
        $completedProject->update($this->validatedProject($request, $completedProject));

        return redirect()->route('dashboard.completed-projects.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(CompletedProject $completedProject): RedirectResponse
    {
        $completedProject->delete();

        return redirect()->route('dashboard.completed-projects.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }

    /**
     * @return array{programs: Collection<int, Program>, regions: Collection<int, Region>}
     */
    private function formOptions(): array
    {
        return [
            'programs' => Program::query()->orderBy('order')->get(['id', 'title_ar']),
            'regions' => Region::query()->orderBy('order')->get(['id', 'name_ar']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedProject(Request $request, ?CompletedProject $completedProject = null): array
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'alpha_dash', Rule::unique('completed_projects', 'key')->ignore($completedProject)],
            'program_id' => ['nullable', 'exists:programs,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'completed_at' => ['nullable', 'date', 'before_or_equal:today'],
            'beneficiaries' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,under_review,published'],
            ...MediaGalleryInput::rules(),
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = 'storage/'.$request->file('image_file')->store('completed-projects', 'public');
        }

        if (empty($validated['key'])) {
            $validated['key'] = $completedProject?->key ?? (Str::slug($validated['title_en'] ?? '') ?: 'project-'.Str::lower(Str::random(6)));
        }

        $validated['order'] ??= 0;
        unset($validated['image_file'], $validated['gallery_files'], $validated['gallery_remove']);

        return [...$validated, ...MediaGalleryInput::apply($request, $completedProject?->galleryPhotos() ?? [], 'completed-projects/gallery')];
    }
}
