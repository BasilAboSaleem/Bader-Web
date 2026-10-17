<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sponsor names typed as free text become sponsor records with a sponsorship each (amounts were recorded in US dollars).
 * A widow family's single amount is kept on the mother; the team can split it between her and the children afterwards.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sponsorIds = [];
        $sponsorFor = function (string $name) use (&$sponsorIds): int {
            $name = trim(preg_replace('/\s+/u', ' ', $name)) ?: 'كافل بدون اسم';

            if (! isset($sponsorIds[$name])) {
                $id = DB::table('sponsors')->insertGetId(['name' => $name, 'preferred_currency' => 'USD', 'created_at' => now(), 'updated_at' => now()]);
                DB::table('sponsors')->where('id', $id)->update(['code' => 'SPN-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT)]);
                $sponsorIds[$name] = $id;
            }

            return $sponsorIds[$name];
        };
        $sponsorship = fn (object $record, int $familyId, ?int $orphanId, string $beneficiary): array => [
            'sponsor_id' => $sponsorFor($record->sponsor_name),
            'orphan_family_id' => $familyId,
            'orphan_id' => $orphanId,
            'beneficiary_type' => $beneficiary,
            'monthly_amount' => $record->sponsorship_monthly_amount ?? 0,
            'currency' => 'USD',
            'started_at' => $record->sponsorship_started_at ?? substr((string) $record->updated_at, 0, 10),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::transaction(function () use ($sponsorship): void {
            DB::table('orphan_families')
                ->where('type', 'widow_family')
                ->where('sponsorship_status', 'sponsored')
                ->whereNotNull('sponsor_name')
                ->orderBy('id')
                ->get()
                ->each(fn (object $family) => DB::table('sponsorships')->insert($sponsorship($family, $family->id, null, 'mother')));

            DB::table('orphans')
                ->join('orphan_families', 'orphan_families.id', '=', 'orphans.orphan_family_id')
                ->where('orphan_families.type', 'orphan')
                ->where('orphans.sponsorship_status', 'sponsored')
                ->whereNotNull('orphans.sponsor_name')
                ->orderBy('orphans.id')
                ->get(['orphans.*'])
                ->each(fn (object $orphan) => DB::table('sponsorships')->insert($sponsorship($orphan, $orphan->orphan_family_id, $orphan->id, 'orphan')));
        });

        foreach (['orphan_families', 'orphans'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropColumn(['sponsor_name', 'sponsorship_monthly_amount', 'sponsorship_started_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['orphan_families', 'orphans'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->string('sponsor_name')->nullable()->after('sponsorship_status');
                $table->decimal('sponsorship_monthly_amount', 10, 2)->nullable()->after('sponsor_name');
                $table->date('sponsorship_started_at')->nullable()->after('sponsorship_monthly_amount');
            });
        }
    }
};
