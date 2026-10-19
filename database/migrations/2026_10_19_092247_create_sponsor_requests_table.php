<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sponsorship_id')->constrained()->restrictOnDelete();
            $table->foreignId('sponsor_id')->constrained()->restrictOnDelete();
            $table->string('type', 30);
            $table->decimal('requested_amount', 12, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['sponsor_id', 'status']);
            $table->index(['sponsorship_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_requests');
    }
};
