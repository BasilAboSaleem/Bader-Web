<?php

namespace App\Http\Controllers;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\SponsorshipCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SponsorshipPageController extends Controller
{
    /** Filters offered on the page: orphans and widows' families come from approved orphan files, students from sponsorship cases. */
    public const FILTER_TYPES = [OrphanFamily::TYPE_ORPHAN, OrphanFamily::TYPE_WIDOW_FAMILY, 'student'];

    private const PER_PAGE = 12;

    public function index(Request $request): View
    {
        $selectedType = in_array($request->query('type'), self::FILTER_TYPES, true) ? $request->query('type') : null;
        $shows = fn (string $type): bool => $selectedType === null || $selectedType === $type;

        $orphans = $shows(OrphanFamily::TYPE_ORPHAN) ? $this->approvedOrphans() : collect();
        $families = $shows(OrphanFamily::TYPE_WIDOW_FAMILY) ? $this->approvedWidowFamilies() : collect();
        $students = $shows('student') ? SponsorshipCase::available()->longestWaiting()->with('region')->get() : collect();

        /** @var Collection<int, array{orphan?: Orphan, family?: OrphanFamily, case?: SponsorshipCase, available: bool}> $entries */
        $entries = $orphans->map(fn (Orphan $orphan): array => ['orphan' => $orphan, 'available' => $orphan->isAvailable()])
            ->concat($families->map(fn (OrphanFamily $family): array => ['family' => $family, 'available' => $family->isAvailable()]))
            ->concat($students->map(fn (SponsorshipCase $case): array => ['case' => $case, 'available' => true]))
            ->sortByDesc('available')
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $cases = new LengthAwarePaginator($entries->forPage($page, self::PER_PAGE)->values(), $entries->count(), self::PER_PAGE, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        $stats = $this->stats();

        return view('pages.sponsorship', compact('cases', 'selectedType', 'stats'));
    }

    public function show(string $code): View
    {
        $case = SponsorshipCase::visible()
            ->where('code', $code)
            ->with('region')
            ->firstOrFail();

        $otherCases = SponsorshipCase::available()
            ->whereKeyNot($case->getKey())
            ->longestWaiting()
            ->with('region')
            ->take(3)
            ->get();

        return view('pages.sponsorship-show', compact('case', 'otherCases'));
    }

    /**
     * @return Collection<int, Orphan>
     */
    private function approvedOrphans(): Collection
    {
        return Orphan::query()
            ->whereHas('family', fn (Builder $query) => $query->approved()->where('type', OrphanFamily::TYPE_ORPHAN))
            ->with('family:id,code,type,sponsorship_status')
            ->latest('id')
            ->get(['id', 'orphan_family_id', 'full_name', 'birth_date', 'sponsorship_status']);
    }

    /**
     * @return Collection<int, OrphanFamily>
     */
    private function approvedWidowFamilies(): Collection
    {
        return OrphanFamily::approved()
            ->where('type', OrphanFamily::TYPE_WIDOW_FAMILY)
            ->with('orphans:id,orphan_family_id,birth_date')
            ->latest('id')
            ->get(['id', 'code', 'type', 'mother_full_name', 'sponsorship_status']);
    }

    /**
     * @return array{waiting: int, sponsored: int, from: mixed}
     */
    private function stats(): array
    {
        $orphanCounts = Orphan::query()
            ->whereHas('family', fn (Builder $query) => $query->approved()->where('type', OrphanFamily::TYPE_ORPHAN))
            ->selectRaw('sponsorship_status, count(*) as total')
            ->groupBy('sponsorship_status')
            ->pluck('total', 'sponsorship_status');

        $familyCounts = OrphanFamily::approved()
            ->where('type', OrphanFamily::TYPE_WIDOW_FAMILY)
            ->selectRaw('sponsorship_status, count(*) as total')
            ->groupBy('sponsorship_status')
            ->pluck('total', 'sponsorship_status');

        $countOf = fn (string $status): int => (int) ($orphanCounts[$status] ?? 0) + (int) ($familyCounts[$status] ?? 0);

        return [
            'waiting' => $countOf(OrphanFamily::SPONSORSHIP_AVAILABLE) + SponsorshipCase::available()->count(),
            'sponsored' => $countOf(OrphanFamily::SPONSORSHIP_SPONSORED) + SponsorshipCase::visible()->where('status', SponsorshipCase::STATUS_SPONSORED)->count(),
            'from' => SponsorshipCase::available()->min('monthly_amount'),
        ];
    }
}
