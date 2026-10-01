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
            $table->string('frequency', 20)->default('once')->after('amount');
            $table->string('stripe_subscription_id')->nullable()->index()->after('stripe_payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['stripe_subscription_id']);
            $table->dropColumn(['frequency', 'stripe_subscription_id']);
        });
    }
};
