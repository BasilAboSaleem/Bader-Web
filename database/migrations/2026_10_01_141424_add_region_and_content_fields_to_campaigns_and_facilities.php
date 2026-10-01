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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('key')->constrained('programs')->nullOnDelete();
            $table->foreignId('region_id')->nullable()->after('program_id')->constrained('regions')->nullOnDelete();
            $table->longText('content_ar')->nullable()->after('description_en');
            $table->longText('content_en')->nullable()->after('content_ar');
            $table->json('preset_amounts')->nullable()->after('raised_amount');
            $table->boolean('allows_monthly')->default(true)->after('preset_amounts');
            $table->unsignedInteger('order')->default(0)->after('is_featured');
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('key')->constrained('regions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('region_id');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
            $table->dropConstrainedForeignId('region_id');
            $table->dropColumn(['content_ar', 'content_en', 'preset_amounts', 'allows_monthly', 'order']);
        });
    }
};
