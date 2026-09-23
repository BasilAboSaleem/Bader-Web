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
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->index(); // contact, partnership, volunteer, sponsorship, assistance, donation_transfer
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('organization')->nullable();
            $table->string('location')->nullable();
            $table->json('payload')->nullable();
            $table->string('status', 30)->default('unread')->index(); // unread, in_progress, resolved, archived
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
