<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description_en');
            $table->string('category_ar')->nullable()->after('image');
            $table->string('category_en')->nullable()->after('category_ar');
            $table->string('badge_ar')->nullable()->after('category_en');
            $table->string('badge_en')->nullable()->after('badge_ar');
            $table->string('highlight_ar')->nullable()->after('badge_en');
            $table->string('highlight_en')->nullable()->after('highlight_ar');
            $table->boolean('is_flagship')->default(false)->after('highlight_en');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'image', 'category_ar', 'category_en',
                'badge_ar', 'badge_en', 'highlight_ar', 'highlight_en', 'is_flagship',
            ]);
        });
    }
};
