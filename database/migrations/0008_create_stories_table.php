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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('excerpt_ar')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('content_ar')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('category_ar')->nullable();
            $table->string('category_en')->nullable();
            $table->date('published_at')->nullable();
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
        Schema::dropIfExists('stories');
    }
};
