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
        foreach (['programs', 'stories', 'campaigns'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->json('gallery')->nullable()->after('image');
                $table->json('videos')->nullable()->after('gallery');
            });
        }

        Schema::table('stories', function (Blueprint $table): void {
            $table->foreignId('program_id')->nullable()->after('key')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('program_id');
        });

        foreach (['programs', 'stories', 'campaigns'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn(['gallery', 'videos']);
            });
        }
    }
};
