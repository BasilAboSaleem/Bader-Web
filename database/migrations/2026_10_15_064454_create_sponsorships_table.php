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
        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_id')->constrained()->restrictOnDelete();
            $table->foreignId('orphan_family_id')->constrained()->restrictOnDelete();
            $table->foreignId('orphan_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('beneficiary_type', 10);
            $table->decimal('monthly_amount', 12, 2);
            $table->string('currency', 3);
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ended_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['sponsor_id', 'ended_at']);
            $table->index(['orphan_family_id', 'ended_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
    }
};
