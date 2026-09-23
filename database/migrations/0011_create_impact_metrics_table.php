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
        Schema::create('impact_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('value'); // e.g. '150,000+'
            $table->string('unit_ar')->nullable(); // e.g. 'وجبة ساخنة'
            $table->string('unit_en')->nullable(); // e.g. 'Hot Meals'
            $table->string('category', 100)->nullable(); // water, food, shelter, health, etc.
            $table->integer('order')->default(0);
            $table->boolean('is_approved')->default(false)->index();
            $table->timestamp('approved_at')->nullable();
            $table->string('status', 30)->default('draft'); // draft, approved, archived
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impact_metrics');
    }
};
