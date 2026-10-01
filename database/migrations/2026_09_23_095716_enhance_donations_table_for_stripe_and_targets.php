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
        Schema::table('donations', function (Blueprint $table) {
            $table->string('target_type', 40)->default('general')->after('donor_phone');
            $table->foreignId('program_id')->nullable()->after('campaign_id')->constrained('programs')->nullOnDelete();
            $table->foreignId('facility_id')->nullable()->after('program_id')->constrained('facilities')->nullOnDelete();
            $table->string('donation_category', 50)->default('general')->after('facility_id');
            $table->string('payment_gateway', 40)->default('manual_transfer')->after('payment_method');
            $table->string('stripe_session_id')->nullable()->index()->after('payment_gateway');
            $table->string('stripe_payment_intent_id')->nullable()->index()->after('stripe_session_id');
            $table->string('receipt_number', 50)->nullable()->unique()->after('id');
            $table->boolean('is_anonymous')->default(false)->after('donor_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropForeign(['facility_id']);
            $table->dropColumn([
                'receipt_number',
                'target_type',
                'program_id',
                'facility_id',
                'donation_category',
                'payment_gateway',
                'stripe_session_id',
                'stripe_payment_intent_id',
                'is_anonymous',
            ]);
        });
    }
};
