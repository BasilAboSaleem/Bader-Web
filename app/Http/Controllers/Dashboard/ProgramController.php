<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        $programs = Program::query()->orderBy('order')->paginate(15);

        return view('dashboard.programs.index', compact('programs'));
    }

    public function create(): View
    {
        return view('dashboard.programs.form', [
            'program' => new Program,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:programs,key'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'integer'],
            'status' => ['required', 'in:draft,under_review,published'],
        ]);

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['title_en'] ?? $validated['title_ar']) ?: 'prog-'.time();
        }

        Program::create($validated);

        return redirect()->route('dashboard.programs.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(Program $program): View
    {
        return view('dashboard.programs.form', [
            'program' => $program,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'unique:programs,key,'.$program->id],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'integer'],
            'status' => ['required', 'in:draft,under_review,published'],
        ]);

        $program->update($validated);

        return redirect()->route('dashboard.programs.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return redirect()->route('dashboard.programs.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }
}
