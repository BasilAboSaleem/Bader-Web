<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->text('content_ar')->nullable()->after('description_en');
            $table->text('content_en')->nullable()->after('content_ar');
            $table->json('gallery')->nullable()->after('content_en');
            $table->string('video_url')->nullable()->after('gallery');
            $table->string('established_year')->nullable()->after('video_url');
            $table->string('capacity_ar')->nullable()->after('established_year');
            $table->string('capacity_en')->nullable()->after('capacity_ar');
        });
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn([
                'content_ar', 'content_en', 'gallery',
                'video_url', 'established_year', 'capacity_ar', 'capacity_en',
            ]);
        });
    }
};
