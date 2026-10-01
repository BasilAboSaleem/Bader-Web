<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()->orderBy('order')->orderBy('id')->paginate(20);

        return view('dashboard.faqs.index', compact('faqs'));
    }

    public function create(): View
    {
        return view('dashboard.faqs.form', [
            'faq' => new Faq(['status' => 'published', 'order' => (int) Faq::max('order') + 1]),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Faq::create($this->validatedFaq($request));

        return redirect()->route('dashboard.faqs.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(Faq $faq): View
    {
        return view('dashboard.faqs.form', [
            'faq' => $faq,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($this->validatedFaq($request));

        return redirect()->route('dashboard.faqs.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('dashboard.faqs.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedFaq(Request $request): array
    {
        $validated = $request->validate([
            'question_ar' => ['required', 'string', 'max:255'],
            'question_en' => ['nullable', 'string', 'max:255'],
            'answer_ar' => ['required', 'string', 'max:5000'],
            'answer_en' => ['nullable', 'string', 'max:5000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $validated['order'] ??= 0;

        return $validated;
    }
}
