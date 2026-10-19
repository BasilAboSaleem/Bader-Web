<?php

namespace App\Support;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\SponsorRequest;
use App\Models\Sponsorship;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * The only place sponsorships are started, changed or ended. A beneficiary has at most one active sponsorship;
 * any change closes the active row (keeping its history) and opens a new one, and the sponsored flags that the
 * public pages read are kept in step in the same transaction.
 */
class SponsorshipManager
{
    /**
     * Apply the dashboard sponsorship form of a widow's family: one sponsor for the family, with an amount for the
     * mother and for each child. Beneficiaries left without an amount are not sponsored.
     *
     * @param  array{sponsorship_status: string, sponsor_id?: int|string|null, currency?: ?string, started_at?: ?string, renewal_type?: ?string, planned_ended_at?: ?string, mother_amount?: mixed, orphans?: array<int|string, array{monthly_amount?: mixed}>}  $input
     */
    public static function applyToWidowFamily(OrphanFamily $family, array $input, User $by): void
    {
        DB::transaction(function () use ($family, $input, $by): void {
            $today = CarbonImmutable::today();

            if ($input['sponsorship_status'] !== OrphanFamily::SPONSORSHIP_SPONSORED) {
                self::release($family, null, $today, $by);
                $family->orphans->each(fn (Orphan $orphan) => self::release($family, $orphan, $today, $by));
                $family->update(['sponsorship_status' => OrphanFamily::SPONSORSHIP_AVAILABLE]);

                return;
            }

            $terms = self::termsFrom($input);

            self::assignOrRelease($family, null, $input['mother_amount'] ?? null, $terms, $by);

            foreach ($family->orphans as $orphan) {
                self::assignOrRelease($family, $orphan, $input['orphans'][$orphan->id]['monthly_amount'] ?? null, $terms, $by);
            }

            $family->update(['sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED]);
        });
    }

    /**
     * Apply the dashboard sponsorship form of an orphan file, where every child has a sponsor of their own.
     * Rows for children outside the file are ignored.
     *
     * @param  array<int|string, array{sponsorship_status: string, sponsor_id?: int|string|null, monthly_amount?: mixed, currency?: ?string, started_at?: ?string, renewal_type?: ?string, planned_ended_at?: ?string}>  $rows
     */
    public static function applyToOrphans(OrphanFamily $family, array $rows, User $by): void
    {
        DB::transaction(function () use ($family, $rows, $by): void {
            foreach ($family->orphans as $orphan) {
                $row = $rows[$orphan->id] ?? null;

                if ($row === null) {
                    continue;
                }

                if ($row['sponsorship_status'] !== OrphanFamily::SPONSORSHIP_SPONSORED) {
                    self::release($family, $orphan, CarbonImmutable::today(), $by);

                    continue;
                }

                self::assign($family, $orphan, $row['monthly_amount'], self::termsFrom($row), $by);
            }
        });
    }

    /**
     * Apply an approved sponsor request: stop the sponsorship, or change its monthly amount.
     */
    public static function applySponsorRequest(SponsorRequest $request, User $by): void
    {
        DB::transaction(function () use ($request, $by): void {
            $sponsorship = Sponsorship::query()->lockForUpdate()->findOrFail($request->sponsorship_id);

            if (! $sponsorship->isActive() || (int) $sponsorship->sponsor_id !== (int) $request->sponsor_id) {
                throw ValidationException::withMessages(['request' => __('sponsors.requests.not_applicable')]);
            }

            $family = $sponsorship->family()->lockForUpdate()->firstOrFail();
            $orphan = $sponsorship->orphan;

            if ($request->type === SponsorRequest::TYPE_STOP) {
                self::release($family, $orphan, CarbonImmutable::today(), $by);

                if ($family->isWidowFamily() && ! $family->activeSponsorships()->exists()) {
                    $family->update(['sponsorship_status' => OrphanFamily::SPONSORSHIP_AVAILABLE]);
                }

                return;
            }

            self::assign($family, $orphan, $request->requested_amount, [
                'sponsor_id' => (int) $sponsorship->sponsor_id,
                'currency' => (string) ($request->currency ?: $sponsorship->currency),
                'started_at' => CarbonImmutable::today(),
                'renewal_type' => $sponsorship->renewal_type,
                'planned_ended_at' => $sponsorship->planned_ended_at
                    ? CarbonImmutable::parse($sponsorship->planned_ended_at->toDateString())
                    : null,
            ], $by);
        });
    }

    /**
     * @param  array{sponsor_id?: int|string|null, currency?: ?string, started_at?: ?string, renewal_type?: ?string, planned_ended_at?: ?string}  $input
     * @return array{sponsor_id: int, currency: string, started_at: CarbonImmutable, renewal_type: string, planned_ended_at: ?CarbonImmutable}
     */
    private static function termsFrom(array $input): array
    {
        $renewalType = (string) ($input['renewal_type'] ?? Sponsorship::RENEWAL_RECURRING);
        $plannedEndedAt = filled($input['planned_ended_at'] ?? null)
            ? CarbonImmutable::parse((string) $input['planned_ended_at'])
            : null;

        if ($renewalType === Sponsorship::RENEWAL_ONE_TIME && $plannedEndedAt === null) {
            throw ValidationException::withMessages(['planned_ended_at' => __('sponsors.planned_end_required')]);
        }

        return [
            'sponsor_id' => (int) $input['sponsor_id'],
            'currency' => (string) $input['currency'],
            'started_at' => CarbonImmutable::parse((string) $input['started_at']),
            'renewal_type' => $renewalType,
            'planned_ended_at' => $plannedEndedAt,
        ];
    }

    /**
     * @param  array{sponsor_id: int, currency: string, started_at: CarbonImmutable, renewal_type: string, planned_ended_at: ?CarbonImmutable}  $terms
     */
    private static function assignOrRelease(OrphanFamily $family, ?Orphan $orphan, mixed $amount, array $terms, User $by): void
    {
        if ($amount === null || $amount === '' || (float) $amount <= 0) {
            self::release($family, $orphan, CarbonImmutable::today(), $by);

            return;
        }

        self::assign($family, $orphan, $amount, $terms, $by);
    }

    /**
     * Start a sponsorship for the beneficiary unless the same terms are already active.
     *
     * @param  array{sponsor_id: int, currency: string, started_at: CarbonImmutable, renewal_type: string, planned_ended_at: ?CarbonImmutable}  $terms
     */
    private static function assign(OrphanFamily $family, ?Orphan $orphan, mixed $amount, array $terms, User $by): void
    {
        $amount = number_format((float) $amount, 2, '.', '');
        $current = self::activeFor($family, $orphan);
        $planned = $terms['planned_ended_at']?->toDateString();

        if (
            $current
            && (int) $current->sponsor_id === $terms['sponsor_id']
            && $current->monthly_amount === $amount
            && $current->currency === $terms['currency']
            && $current->renewal_type === $terms['renewal_type']
            && $current->planned_ended_at?->toDateString() === $planned
        ) {
            $orphan?->update(['sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED]);

            return;
        }

        if ($current) {
            $endsAt = $terms['started_at']->subDay();
            self::close($current, $endsAt->lessThan($current->started_at) ? CarbonImmutable::parse($current->started_at) : $endsAt, $by);
        }

        Sponsorship::create([
            'sponsor_id' => $terms['sponsor_id'],
            'orphan_family_id' => $family->id,
            'orphan_id' => $orphan?->id,
            'beneficiary_type' => $orphan ? Sponsorship::BENEFICIARY_ORPHAN : Sponsorship::BENEFICIARY_MOTHER,
            'monthly_amount' => $amount,
            'currency' => $terms['currency'],
            'started_at' => $terms['started_at'],
            'renewal_type' => $terms['renewal_type'],
            'planned_ended_at' => $planned,
            'created_by' => $by->id,
        ]);

        $orphan?->update(['sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED]);
    }

    private static function release(OrphanFamily $family, ?Orphan $orphan, CarbonImmutable $endsAt, User $by): void
    {
        $current = self::activeFor($family, $orphan);

        if ($current) {
            self::close($current, $endsAt->lessThan($current->started_at) ? CarbonImmutable::parse($current->started_at) : $endsAt, $by);
        }

        $orphan?->update(['sponsorship_status' => OrphanFamily::SPONSORSHIP_AVAILABLE]);
    }

    private static function close(Sponsorship $sponsorship, CarbonImmutable $endsAt, User $by): void
    {
        $sponsorship->update(['ended_at' => $endsAt, 'ended_by' => $by->id]);
    }

    private static function activeFor(OrphanFamily $family, ?Orphan $orphan): ?Sponsorship
    {
        return Sponsorship::query()
            ->where('orphan_family_id', $family->id)
            ->where('beneficiary_type', $orphan ? Sponsorship::BENEFICIARY_ORPHAN : Sponsorship::BENEFICIARY_MOTHER)
            ->where('orphan_id', $orphan?->id)
            ->active()
            ->lockForUpdate()
            ->first();
    }
}
