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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->decimal('goal_amount', 12, 2)->nullable();
            $table->decimal('raised_amount', 12, 2)->default(0);
            $table->string('currency_ar')->default('ريال عماني');
            $table->string('currency_en')->default('OMR');
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('published')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
