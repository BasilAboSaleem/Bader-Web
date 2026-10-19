<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsorships', function (Blueprint $table): void {
            $table->string('renewal_type', 20)->default('recurring')->after('started_at');
            $table->date('planned_ended_at')->nullable()->after('renewal_type');
        });
    }

    public function down(): void
    {
        Schema::table('sponsorships', function (Blueprint $table): void {
            $table->dropColumn(['renewal_type', 'planned_ended_at']);
        });
    }
};
