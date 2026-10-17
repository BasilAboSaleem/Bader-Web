<?php

namespace App\Support;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\Sponsorship;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

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
     * @param  array{sponsorship_status: string, sponsor_id?: int|string|null, currency?: ?string, started_at?: ?string, mother_amount?: mixed, orphans?: array<int|string, array{monthly_amount?: mixed}>}  $input
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

            $terms = [
                'sponsor_id' => (int) $input['sponsor_id'],
                'currency' => (string) $input['currency'],
                'started_at' => CarbonImmutable::parse((string) $input['started_at']),
            ];

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
     * @param  array<int|string, array{sponsorship_status: string, sponsor_id?: int|string|null, monthly_amount?: mixed, currency?: ?string, started_at?: ?string}>  $rows
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

                self::assign($family, $orphan, $row['monthly_amount'], [
                    'sponsor_id' => (int) $row['sponsor_id'],
                    'currency' => (string) $row['currency'],
                    'started_at' => CarbonImmutable::parse((string) $row['started_at']),
                ], $by);
            }
        });
    }

    /**
     * @param  array{sponsor_id: int, currency: string, started_at: CarbonImmutable}  $terms
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
     * Start a sponsorship for the beneficiary unless the same sponsor already pays the same amount in the same currency.
     *
     * @param  array{sponsor_id: int, currency: string, started_at: CarbonImmutable}  $terms
     */
    private static function assign(OrphanFamily $family, ?Orphan $orphan, mixed $amount, array $terms, User $by): void
    {
        $amount = number_format((float) $amount, 2, '.', '');
        $current = self::activeFor($family, $orphan);

        if ($current && (int) $current->sponsor_id === $terms['sponsor_id'] && $current->monthly_amount === $amount && $current->currency === $terms['currency']) {
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
