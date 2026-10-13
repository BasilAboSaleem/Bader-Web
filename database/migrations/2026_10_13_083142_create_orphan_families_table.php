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
        Schema::create('orphan_families', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->unique();
            $table->string('type', 20)->index();
            $table->string('source', 20)->default('website');
            $table->string('review_status', 20)->default('pending')->index();
            $table->string('sponsorship_status', 20)->default('available');

            $table->string('contact_name')->nullable();
            $table->string('contact_relation', 100)->nullable();
            $table->string('contact_phone', 30)->nullable();

            $table->string('father_full_name');
            $table->date('father_death_date');
            $table->string('father_death_cause');

            $table->string('mother_full_name')->nullable();
            $table->string('mother_national_id', 20)->nullable();
            $table->date('mother_birth_date')->nullable();
            $table->string('mother_passport_number', 30)->nullable();
            $table->string('mother_residence_area', 50)->nullable();
            $table->string('mother_neighborhood')->nullable();
            $table->string('mother_nearest_landmark')->nullable();
            $table->string('mother_birth_country', 100)->nullable();
            $table->string('mother_birth_place')->nullable();
            $table->string('mother_nationality', 100)->nullable();
            $table->string('mother_phone', 30)->nullable();

            $table->json('documents')->nullable();
            $table->text('review_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orphan_families');
    }
};
