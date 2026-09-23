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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->string('donor_phone')->nullable();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency_ar', 50)->default('ريال عماني');
            $table->string('currency_en', 50)->default('OMR');
            $table->string('payment_method', 50)->default('bank_transfer'); // bank_transfer, cash, cheque, other
            $table->string('reference_number')->nullable();
            $table->date('transfer_date')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('verified')->index(); // pending, verified, rejected
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
