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
        Schema::create('sponsorship_cases', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('type', 20)->index();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender', 10)->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->decimal('monthly_amount', 10, 2);
            $table->unsignedSmallInteger('duration_months')->default(12);
            $table->text('bio_ar')->nullable();
            $table->text('bio_en')->nullable();
            $table->string('photo')->nullable();
            $table->string('status', 20)->default('available')->index();
            $table->date('waiting_since')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorship_cases');
    }
};
