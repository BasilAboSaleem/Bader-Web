<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Support\OrphanFamilyInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrphanFamilyController extends Controller
{
    public function index(Request $request): View
    {
        $statusCounts = OrphanFamily::query()
            ->selectRaw('review_status, count(*) as total')
            ->groupBy('review_status')
            ->pluck('total', 'review_status');

        $currentStatus = match (true) {
            in_array($request->query('status'), OrphanFamily::REVIEW_STATUSES, true) => $request->query('status'),
            ($statusCounts[OrphanFamily::REVIEW_PENDING] ?? 0) > 0 => OrphanFamily::REVIEW_PENDING,
            default => OrphanFamily::REVIEW_APPROVED,
        };
        $search = trim((string) $request->query('q'));

        $families = OrphanFamily::query()
            ->where('review_status', $currentStatus)
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search): void {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('father_full_name', 'like', "%{$search}%")
                    ->orWhere('mother_full_name', 'like', "%{$search}%")
                    ->orWhere('mother_national_id', $search)
                    ->orWhereHas('orphans', fn (Builder $orphans) => $orphans->where('full_name', 'like', "%{$search}%")->orWhere('national_id', $search));
            }))
            ->withCount(['orphans', 'orphans as sponsored_orphans_count' => fn (Builder $orphans) => $orphans->where('sponsorship_status', OrphanFamily::SPONSORSHIP_SPONSORED)])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.orphans.index', compact('families', 'currentStatus', 'statusCounts', 'search'));
    }

    public function create(): View
    {
        return view('dashboard.orphans.form', [
            'family' => new OrphanFamily(['type' => OrphanFamily::TYPE_ORPHAN]),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(OrphanFamilyInput::rules(documentsRequired: false), [], OrphanFamilyInput::attributes());

        $family = DB::transaction(function () use ($request, $validated): OrphanFamily {
            $family = OrphanFamily::create([
                ...OrphanFamilyInput::familyAttributes($validated),
                'source' => OrphanFamily::SOURCE_DASHBOARD,
                'review_status' => OrphanFamily::REVIEW_PENDING,
            ]);

            OrphanFamilyInput::syncOrphans($family, $validated['orphans']);
            OrphanFamilyInput::storeDocuments($request, $family);

            return $family;
        });

        return redirect()->route('dashboard.orphans.show', $family)
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function show(OrphanFamily $orphanFamily): View
    {
        $orphanFamily->load(['orphans', 'reviewer']);

        return view('dashboard.orphans.show', [
            'family' => $orphanFamily,
            'duplicates' => $this->duplicateNationalIds($orphanFamily),
        ]);
    }

    public function edit(OrphanFamily $orphanFamily): View
    {
        return view('dashboard.orphans.form', [
            'family' => $orphanFamily->load('orphans'),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, OrphanFamily $orphanFamily): RedirectResponse
    {
        $validated = $request->validate(OrphanFamilyInput::rules(documentsRequired: false), [], OrphanFamilyInput::attributes());

        DB::transaction(function () use ($request, $validated, $orphanFamily): void {
            $orphanFamily->update(OrphanFamilyInput::familyAttributes($validated));
            OrphanFamilyInput::syncOrphans($orphanFamily, $validated['orphans']);
            OrphanFamilyInput::storeDocuments($request, $orphanFamily);
        });

        return redirect()->route('dashboard.orphans.show', $orphanFamily)
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function destroy(OrphanFamily $orphanFamily): RedirectResponse
    {
        $status = $orphanFamily->review_status;

        OrphanFamilyInput::deleteDocuments($orphanFamily);
        $orphanFamily->delete();

        return redirect()->route('dashboard.orphans.index', ['status' => $status])
            ->with('status', __('dashboard.deleted_successfully'));
    }

    public function review(Request $request, OrphanFamily $orphanFamily): RedirectResponse
    {
        $validated = $request->validate([
            'review_status' => ['required', Rule::in([OrphanFamily::REVIEW_APPROVED, OrphanFamily::REVIEW_REJECTED])],
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $orphanFamily->update([
            ...$validated,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('dashboard.orphans.show', $orphanFamily)
            ->with('status', __('orphans.review_saved.'.$validated['review_status']));
    }

    /**
     * A widow's family is sponsored as one unit; an orphan file is sponsored child by child.
     * Marking anyone as sponsored records the sponsor's name, monthly amount and start date for the team only.
     */
    public function updateSponsorship(Request $request, OrphanFamily $orphanFamily): RedirectResponse
    {
        if (! $orphanFamily->isApproved()) {
            return redirect()->route('dashboard.orphans.show', $orphanFamily)
                ->withErrors(['sponsorship' => __('orphans.sponsorship_needs_approval')]);
        }

        if ($orphanFamily->isWidowFamily()) {
            $validated = $request->validate($this->sponsorRules(''), [], $this->sponsorAttributeNames(''));
            $orphanFamily->update($this->sponsorAttributes($validated));
        } else {
            $validated = $request->validate([
                'orphans' => ['required', 'array'],
                ...$this->sponsorRules('orphans.*.'),
            ], [], $this->sponsorAttributeNames('orphans.*.'));

            foreach ($validated['orphans'] as $orphanId => $sponsorship) {
                Orphan::where('orphan_family_id', $orphanFamily->id)->whereKey($orphanId)->update($this->sponsorAttributes($sponsorship));
            }
        }

        return redirect()->route('dashboard.orphans.show', $orphanFamily)
            ->with('status', __('dashboard.saved_successfully'));
    }

    public function document(OrphanFamily $orphanFamily, string $document): StreamedResponse
    {
        abort_unless(in_array($document, OrphanFamily::DOCUMENTS, true) && $orphanFamily->hasDocument($document), 404);

        $path = $orphanFamily->documents[$document];
        $disk = Storage::disk(OrphanFamilyInput::DOCUMENT_DISK);

        abort_unless($disk->exists($path), 404);

        return $disk->response($path, $orphanFamily->code.'-'.$document.'.'.pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function sponsorRules(string $prefix): array
    {
        $whenSponsored = 'required_if:'.$prefix.'sponsorship_status,'.OrphanFamily::SPONSORSHIP_SPONSORED;

        return [
            $prefix.'sponsorship_status' => ['required', Rule::in(OrphanFamily::SPONSORSHIP_STATUSES)],
            $prefix.'sponsor_name' => [$whenSponsored, 'nullable', 'string', 'max:255'],
            $prefix.'sponsorship_monthly_amount' => [$whenSponsored, 'nullable', 'numeric', 'min:1', 'max:100000'],
            $prefix.'sponsorship_started_at' => [$whenSponsored, 'nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function sponsorAttributeNames(string $prefix): array
    {
        return collect(OrphanFamily::SPONSOR_FIELDS)
            ->mapWithKeys(fn (string $field): array => [$prefix.$field => __('orphans.field.'.$field)])
            ->all();
    }

    /**
     * Sponsor details only make sense while sponsored, so they are cleared when the status goes back to available.
     *
     * @param  array<string, mixed>  $input
     * @return array{sponsorship_status: string, sponsor_name: ?string, sponsorship_monthly_amount: mixed, sponsorship_started_at: ?string}
     */
    private function sponsorAttributes(array $input): array
    {
        $isSponsored = $input['sponsorship_status'] === OrphanFamily::SPONSORSHIP_SPONSORED;

        return [
            'sponsorship_status' => $input['sponsorship_status'],
            'sponsor_name' => $isSponsored ? $input['sponsor_name'] : null,
            'sponsorship_monthly_amount' => $isSponsored ? $input['sponsorship_monthly_amount'] : null,
            'sponsorship_started_at' => $isSponsored ? $input['sponsorship_started_at'] : null,
        ];
    }

    /**
     * National ID numbers in this file that already appear in another file, keyed by number.
     *
     * @return array<string, list<string>>
     */
    private function duplicateNationalIds(OrphanFamily $family): array
    {
        $numbers = $family->orphans->pluck('national_id')->push($family->mother_national_id)->filter()->unique()->values();

        if ($numbers->isEmpty()) {
            return [];
        }

        $duplicates = [];

        Orphan::query()
            ->whereIn('national_id', $numbers)
            ->where('orphan_family_id', '!=', $family->id)
            ->with('family:id,code')
            ->get(['national_id', 'orphan_family_id'])
            ->each(function (Orphan $orphan) use (&$duplicates): void {
                $duplicates[$orphan->national_id][] = $orphan->family->code;
            });

        OrphanFamily::query()
            ->whereIn('mother_national_id', $numbers)
            ->whereKeyNot($family->id)
            ->get(['code', 'mother_national_id'])
            ->each(function (OrphanFamily $other) use (&$duplicates): void {
                $duplicates[$other->mother_national_id][] = $other->code;
            });

        return array_map(fn (array $codes): array => array_values(array_unique($codes)), $duplicates);
    }
}
