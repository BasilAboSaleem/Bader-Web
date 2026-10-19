<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorship_disbursements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sponsorship_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->decimal('usd_rate', 12, 6)->nullable();
            $table->date('paid_on');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['sponsorship_id', 'period_year', 'period_month']);
            $table->index(['period_year', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorship_disbursements');
    }
};
