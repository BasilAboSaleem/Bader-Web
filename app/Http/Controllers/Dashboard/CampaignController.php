<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Program;
use App\Models\Region;
use App\Support\MediaGalleryInput;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::query()->with(['program', 'region'])->latest()->paginate(15);

        return view('dashboard.campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        return view('dashboard.campaigns.form', [
            'campaign' => new Campaign([
                'currency_ar' => 'دولار أمريكي',
                'currency_en' => 'USD',
                'allows_monthly' => true,
                'preset_amounts' => Campaign::DEFAULT_PRESET_AMOUNTS,
                'status' => 'published',
            ]),
            ...$this->formOptions(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Campaign::create($this->validatedCampaign($request));

        return redirect()->route('dashboard.campaigns.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function edit(Campaign $campaign): View
    {
        return view('dashboard.campaigns.form', [
            'campaign' => $campaign,
            ...$this->formOptions(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update($this->validatedCampaign($request, $campaign));

        return redirect()->route('dashboard.campaigns.index')
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return redirect()->route('dashboard.campaigns.index')
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
    private function validatedCampaign(Request $request, ?Campaign $campaign = null): array
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:100', 'alpha_dash', Rule::unique('campaigns', 'key')->ignore($campaign)],
            'program_id' => ['nullable', 'exists:programs,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'goal_amount' => ['nullable', 'numeric', 'min:0'],
            'raised_amount' => ['nullable', 'numeric', 'min:0'],
            'preset_amounts' => ['nullable', 'string', 'max:100', 'regex:/^\s*\d+(\.\d+)?(\s*,\s*\d+(\.\d+)?)*\s*$/'],
            'currency_ar' => ['nullable', 'string', 'max:50'],
            'currency_en' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,under_review,published'],
            ...MediaGalleryInput::rules(),
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = 'storage/'.$request->file('image_file')->store('campaigns', 'public');
        }

        if (empty($validated['key'])) {
            $validated['key'] = $campaign?->key ?? (Str::slug($validated['title_en'] ?? '') ?: 'campaign-'.Str::lower(Str::random(6)));
        }

        $validated['preset_amounts'] = filled($validated['preset_amounts'] ?? null)
            ? array_slice(array_values(array_unique(array_map(
                fn (string $amount) => floor((float) $amount) == (float) $amount ? (int) $amount : (float) $amount,
                explode(',', $validated['preset_amounts']),
            ))), 0, 6)
            : null;
        $validated['raised_amount'] ??= 0;
        $validated['order'] ??= 0;
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['allows_monthly'] = $request->boolean('allows_monthly');
        unset($validated['image_file'], $validated['gallery_files'], $validated['gallery_remove']);

        return [...$validated, ...MediaGalleryInput::apply($request, $campaign?->galleryPhotos() ?? [], 'campaigns/gallery')];
    }
}
