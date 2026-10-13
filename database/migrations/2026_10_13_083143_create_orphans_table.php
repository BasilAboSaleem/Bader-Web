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
        Schema::create('orphans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orphan_family_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->date('birth_date');
            $table->string('national_id', 20);
            $table->string('education_stage', 30);
            $table->string('health_status', 30);
            $table->string('health_notes', 500)->nullable();
            $table->string('sponsorship_status', 20)->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orphans');
    }
};
