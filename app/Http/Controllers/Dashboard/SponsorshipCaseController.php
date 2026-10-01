<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\SponsorshipCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SponsorshipCaseController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $cases = SponsorshipCase::query()
            ->with('region')
            ->when(in_array($status, $this->statuses(), true), fn ($query) => $query->where('status', $status))
            ->longestWaiting()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.sponsorship-cases.index', [
            'cases' => $cases,
            'statuses' => $this->statuses(),
            'currentStatus' => $status,
        ]);
    }

    public function create(): View
    {
        return view('dashboard.sponsorship-cases.form', [
            'case' => new SponsorshipCase([
                'type' => 'orphan',
                'status' => SponsorshipCase::STATUS_AVAILABLE,
                'duration_months' => 12,
                'waiting_since' => now(),
            ]),
            'regions' => Region::query()->orderBy('order')->get(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        SponsorshipCase::create($this->validatedCase($request));

        return redirect()->route('dashboard.sponsorship-cases.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(SponsorshipCase $sponsorshipCase): View
    {
        return view('dashboard.sponsorship-cases.form', [
            'case' => $sponsorshipCase,
            'regions' => Region::query()->orderBy('order')->get(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, SponsorshipCase $sponsorshipCase): RedirectResponse
    {
        $sponsorshipCase->update($this->validatedCase($request, $sponsorshipCase));

        return redirect()->route('dashboard.sponsorship-cases.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(SponsorshipCase $sponsorshipCase): RedirectResponse
    {
        $sponsorshipCase->delete();

        return redirect()->route('dashboard.sponsorship-cases.index')
            ->with('status', __('dashboard.deleted_successfully'));
    }

    /**
     * @return list<string>
     */
    private function statuses(): array
    {
        return [SponsorshipCase::STATUS_AVAILABLE, SponsorshipCase::STATUS_SPONSORED, SponsorshipCase::STATUS_HIDDEN];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedCase(Request $request, ?SponsorshipCase $case = null): array
    {
        $request->merge(['code' => Str::upper(trim((string) $request->input('code')))]);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9-]+$/', Rule::unique('sponsorship_cases', 'code')->ignore($case)],
            'type' => ['required', Rule::in(SponsorshipCase::TYPES)],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'between:0,120'],
            'gender' => ['nullable', 'in:male,female'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'monthly_amount' => ['required', 'numeric', 'min:1', 'max:100000'],
            'duration_months' => ['required', 'integer', 'between:1,240'],
            'bio_ar' => ['nullable', 'string', 'max:5000'],
            'bio_en' => ['nullable', 'string', 'max:5000'],
            'photo' => ['nullable', 'string', 'max:255'],
            'photo_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'status' => ['required', Rule::in($this->statuses())],
            'waiting_since' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('photo_file')) {
            $validated['photo'] = 'storage/'.$request->file('photo_file')->store('sponsorship-cases', 'public');
        }

        unset($validated['photo_file']);

        return $validated;
    }
}
