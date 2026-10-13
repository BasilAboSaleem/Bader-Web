<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['orphan_families', 'orphans'] as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->string('sponsor_name')->nullable()->after('sponsorship_status');
                $table->decimal('sponsorship_monthly_amount', 10, 2)->nullable()->after('sponsor_name');
                $table->date('sponsorship_started_at')->nullable()->after('sponsorship_monthly_amount');
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
                $table->dropColumn(['sponsor_name', 'sponsorship_monthly_amount', 'sponsorship_started_at']);
            });
        }
    }
};
