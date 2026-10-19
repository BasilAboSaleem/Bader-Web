<?php

namespace App\Http\Controllers;

use App\Models\OrphanFamily;
use App\Support\OrphanFamilyInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

/**
 * Public self-service: find a sponsorship file by national ID, then complete or correct its details.
 * Access is session-gated after a successful lookup (not a password); staff re-review every change.
 */
class OrphanCaseUpdateController extends Controller
{
    public const HONEYPOT_FIELD = 'website';

    public const SESSION_FAMILY_ID = 'orphan_case_update_family_id';

    public const SESSION_VERIFIED_AT = 'orphan_case_update_verified_at';

    public const SESSION_TTL_MINUTES = 60;

    public function create(): View
    {
        return view('pages.orphan-case-update', [
            'updated' => session('orphan_case_updated', false),
        ]);
    }

    public function lookup(Request $request): RedirectResponse
    {
        if ($request->filled(self::HONEYPOT_FIELD)) {
            return redirect()->route('orphan-case-update')
                ->withErrors(['national_id' => __('orphan_case_update.not_found')]);
        }

        $validated = $request->validate([
            'national_id' => ['required', 'digits:9'],
        ], [], [
            'national_id' => __('orphan_case_update.national_id'),
        ]);

        $family = $this->findFamilyByNationalId($validated['national_id']);

        if (! $family) {
            return redirect()->route('orphan-case-update')
                ->withInput()
                ->withErrors(['national_id' => __('orphan_case_update.not_found')]);
        }

        $request->session()->put([
            self::SESSION_FAMILY_ID => $family->id,
            self::SESSION_VERIFIED_AT => now()->getTimestamp(),
        ]);

        return redirect()->route('orphan-case-update.edit');
    }

    public function edit(Request $request): View|RedirectResponse
    {
        $family = $this->authorizedFamily($request);

        if (! $family) {
            return $this->sessionExpired();
        }

        $family->load('orphans');

        return view('pages.orphan-case-update-edit', [
            'family' => $family,
            'submissionToken' => (string) Str::uuid(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $family = $this->authorizedFamily($request);

        if (! $family) {
            return $this->sessionExpired();
        }

        if ($request->filled(self::HONEYPOT_FIELD)) {
            return $this->finishUpdate($request);
        }

        $request->merge(['type' => $family->type]);

        if ($family->type === OrphanFamily::TYPE_WIDOW_FAMILY && filled($family->mother_national_id)) {
            $request->merge(['mother_national_id' => $family->mother_national_id]);
        }

        $validated = $request->validate(
            OrphanFamilyInput::publicUpdateRules($family),
            [],
            OrphanFamilyInput::attributes(),
        );

        $this->guardSponsorshipHistory($family, $validated);

        $tokenKey = Str::isUuid($token = (string) $request->input('submission_token'))
            ? 'orphan-case-update:'.$token
            : null;

        if ($tokenKey && ! Cache::add($tokenKey, true, now()->addDay())) {
            return $this->finishUpdate($request);
        }

        try {
            DB::transaction(function () use ($request, $family, $validated): void {
                $family->update([
                    ...OrphanFamilyInput::familyAttributes($validated),
                    'review_status' => OrphanFamily::REVIEW_PENDING,
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                ]);

                OrphanFamilyInput::syncOrphans($family, $validated['orphans']);
                OrphanFamilyInput::storeDocuments($request, $family);
            });
        } catch (Throwable $exception) {
            if ($tokenKey) {
                Cache::forget($tokenKey);
            }

            throw $exception;
        }

        return $this->finishUpdate($request);
    }

    public function exit(Request $request): RedirectResponse
    {
        $this->forgetSession($request);

        return redirect()->route('orphan-case-update');
    }

    private function findFamilyByNationalId(string $nationalId): ?OrphanFamily
    {
        return OrphanFamily::query()
            ->where('mother_national_id', $nationalId)
            ->orWhereHas('orphans', fn ($query) => $query->where('national_id', $nationalId))
            ->first();
    }

    private function authorizedFamily(Request $request): ?OrphanFamily
    {
        $familyId = $request->session()->get(self::SESSION_FAMILY_ID);
        $verifiedAt = $request->session()->get(self::SESSION_VERIFIED_AT);

        if (! is_int($familyId) && ! ctype_digit((string) $familyId)) {
            return null;
        }

        if (! is_int($verifiedAt) || now()->getTimestamp() - $verifiedAt > self::SESSION_TTL_MINUTES * 60) {
            $this->forgetSession($request);

            return null;
        }

        return OrphanFamily::query()->find((int) $familyId);
    }

    /**
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

    private function finishUpdate(Request $request): RedirectResponse
    {
        $this->forgetSession($request);

        return redirect()->route('orphan-case-update')->with('orphan_case_updated', true);
    }

    private function sessionExpired(): RedirectResponse
    {
        return redirect()->route('orphan-case-update')
            ->withErrors(['national_id' => __('orphan_case_update.session_expired')]);
    }

    private function forgetSession(Request $request): void
    {
        $request->session()->forget([self::SESSION_FAMILY_ID, self::SESSION_VERIFIED_AT]);
    }
}
