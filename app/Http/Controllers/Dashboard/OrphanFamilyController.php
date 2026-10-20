<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\SponsorshipDisbursement;
use App\Support\OrphanFamilyInput;
use App\Support\OrphanImport;
use App\Support\SponsorshipFinanceReport;
use App\Support\SponsorshipManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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

    public function importForm(): View
    {
        return view('dashboard.orphans.import', [
            'columns' => OrphanImport::COLUMNS,
            'types' => OrphanImport::allowedTypeLabels(),
            'educationStages' => OrphanImport::allowedEducationLabels(),
            'healthStatuses' => OrphanImport::allowedHealthLabels(),
            'residenceAreas' => OrphanImport::allowedResidenceLabels(),
        ]);
    }

    public function importTemplate(): Response
    {
        return response(OrphanImport::templateBytes(), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="bader-orphans-import.xlsx"',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:5120'],
        ], [], ['file' => __('orphans.import.file')]);

        $result = OrphanImport::import($request->file('file'), $request->user());

        return redirect()->route('dashboard.orphans.index', ['status' => OrphanFamily::REVIEW_APPROVED])
            ->with('status', __('orphans.import.success', $result));
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

    public function show(Request $request, OrphanFamily $orphanFamily): View
    {
        $orphanFamily->load([
            'orphans.activeSponsorship',
            'reviewer',
            'activeSponsorships.sponsor',
            'activeSponsorships.orphan',
            'activeSponsorships.family',
            'sponsorships' => fn ($query) => $query->with('sponsor')->orderByDesc('started_at'),
        ]);

        $disbursements = SponsorshipDisbursement::query()
            ->whereHas('sponsorship', fn ($query) => $query->where('orphan_family_id', $orphanFamily->id))
            ->with(['sponsorship.sponsor', 'sponsorship.orphan'])
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->get();

        return view('dashboard.orphans.show', [
            'family' => $orphanFamily,
            'duplicates' => $this->duplicateNationalIds($orphanFamily),
            'sponsorName' => SponsorshipFinanceReport::primarySponsorName($orphanFamily),
            'approvedAmounts' => SponsorshipFinanceReport::approvedAmounts($orphanFamily),
            'monthRows' => SponsorshipFinanceReport::caseMonthRows($disbursements),
            'financeTotals' => $disbursements->groupBy('currency')->map->sum(fn ($row): float => (float) $row->amount),
            'sponsors' => $request->user()->canManageFinance()
                ? Sponsor::query()->orderBy('name')->get(['id', 'code', 'name', 'preferred_currency'])
                : null,
            'canManageOrphans' => $request->user()->canManageOrphans(),
            'canManageFinance' => $request->user()->canManageFinance(),
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
        $this->guardSponsorshipHistory($orphanFamily, $validated);

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
        if ($orphanFamily->sponsorships()->exists()) {
            return redirect()->route('dashboard.orphans.show', $orphanFamily)
                ->withErrors(['sponsorship' => __('sponsors.family_has_history')]);
        }

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

        if ($validated['review_status'] !== OrphanFamily::REVIEW_APPROVED && $orphanFamily->activeSponsorships()->exists()) {
            return redirect()->route('dashboard.orphans.show', $orphanFamily)
                ->withErrors(['sponsorship' => __('sponsors.end_before_rejecting')]);
        }

        $orphanFamily->update([
            ...$validated,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('dashboard.orphans.show', $orphanFamily)
            ->with('status', __('orphans.review_saved.'.$validated['review_status']));
    }

    /**
     * A widow's family has one sponsor paying an amount for the mother and for each child; an orphan file is
     * sponsored child by child. The sponsor is always picked from the registered sponsors.
     */
    public function updateSponsorship(Request $request, OrphanFamily $orphanFamily): RedirectResponse
    {
        if (! $orphanFamily->isApproved()) {
            return redirect()->route('dashboard.orphans.show', $orphanFamily)
                ->withErrors(['sponsorship' => __('orphans.sponsorship_needs_approval')]);
        }

        $orphanFamily->load('orphans');

        if ($orphanFamily->isWidowFamily()) {
            $validated = $request->validate([
                ...$this->sponsorRules(''),
                'mother_amount' => ['nullable', 'numeric', 'min:0', 'max:100000'],
                'orphans' => ['nullable', 'array'],
                'orphans.*.monthly_amount' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            ], [], [
                ...$this->sponsorAttributeNames(''),
                'mother_amount' => __('sponsors.beneficiary.mother'),
                'orphans.*.monthly_amount' => __('sponsors.field.monthly_amount'),
            ]);

            $amounts = collect($validated['orphans'] ?? [])->pluck('monthly_amount')->push($validated['mother_amount'] ?? null);

            if ($validated['sponsorship_status'] === OrphanFamily::SPONSORSHIP_SPONSORED && $amounts->every(fn (mixed $amount): bool => (float) $amount <= 0)) {
                throw ValidationException::withMessages(['mother_amount' => __('sponsors.amount_required')]);
            }

            SponsorshipManager::applyToWidowFamily($orphanFamily, $validated, $request->user());
        } else {
            $validated = $request->validate([
                'orphans' => ['required', 'array'],
                ...$this->sponsorRules('orphans.*.'),
                'orphans.*.monthly_amount' => ['required_if:orphans.*.sponsorship_status,'.OrphanFamily::SPONSORSHIP_SPONSORED, 'nullable', 'numeric', 'min:1', 'max:100000'],
            ], [], [
                ...$this->sponsorAttributeNames('orphans.*.'),
                'orphans.*.monthly_amount' => __('sponsors.field.monthly_amount'),
            ]);

            SponsorshipManager::applyToOrphans($orphanFamily, $validated['orphans'], $request->user());
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

    public function photo(OrphanFamily $orphanFamily, Orphan $orphan): StreamedResponse
    {
        abort_unless($orphan->orphan_family_id === $orphanFamily->id && filled($orphan->photo), 404);

        $disk = Storage::disk(OrphanFamilyInput::DOCUMENT_DISK);

        abort_unless($disk->exists($orphan->photo), 404);

        return $disk->response($orphan->photo, $orphanFamily->code.'-'.$orphan->id.'.jpg', [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function sponsorRules(string $prefix): array
    {
        $whenSponsored = 'required_if:'.$prefix.'sponsorship_status,'.OrphanFamily::SPONSORSHIP_SPONSORED;

        return [
            $prefix.'sponsorship_status' => ['required', Rule::in(OrphanFamily::SPONSORSHIP_STATUSES)],
            $prefix.'sponsor_id' => [$whenSponsored, 'nullable', 'integer', Rule::exists('sponsors', 'id')],
            $prefix.'currency' => [$whenSponsored, 'nullable', Rule::in(config('bader.sponsorship_currencies'))],
            $prefix.'started_at' => [$whenSponsored, 'nullable', 'date', 'after_or_equal:2000-01-01', 'before_or_equal:'.now()->addYear()->toDateString()],
            $prefix.'renewal_type' => ['nullable', Rule::in(Sponsorship::RENEWAL_TYPES)],
            $prefix.'planned_ended_at' => ['nullable', 'date', 'after_or_equal:'.$prefix.'started_at', 'before_or_equal:'.now()->addYears(10)->toDateString()],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function sponsorAttributeNames(string $prefix): array
    {
        return collect([
            'sponsorship_status' => 'orphans.sponsorship_title',
            'sponsor_id' => 'sponsors.field.sponsor',
            'currency' => 'sponsors.field.currency',
            'started_at' => 'sponsors.field.started_at',
            'renewal_type' => 'sponsors.field.renewal_type',
            'planned_ended_at' => 'sponsors.field.planned_ended_at',
        ])
            ->mapWithKeys(fn (string $label, string $field): array => [$prefix.$field => __($label)])
            ->all();
    }

    /**
     * Sponsorship records are financial history: a child who was ever sponsored stays in the file, and a file
     * with running sponsorships keeps its type (a widow's family sponsors the mother, an orphan file does not).
     *
     * @param  array<string, mixed>  $validated
     */
    private function guardSponsorshipHistory(OrphanFamily $family, array $validated): void
    {
        $keptOrphanIds = collect($validated['orphans'])->pluck('id')->filter()->map(fn (mixed $id): int => (int) $id);

        $removesSponsoredOrphan = $family->sponsorships()
            ->whereNotNull('orphan_id')
            ->whereNotIn('orphan_id', $keptOrphanIds)
            ->exists();

        if ($removesSponsoredOrphan) {
            throw ValidationException::withMessages(['orphans' => __('sponsors.orphan_has_history')]);
        }

        if ($validated['type'] !== $family->type && $family->activeSponsorships()->exists()) {
            throw ValidationException::withMessages(['type' => __('sponsors.type_locked')]);
        }
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
