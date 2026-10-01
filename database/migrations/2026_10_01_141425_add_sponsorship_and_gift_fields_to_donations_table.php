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
            $table->foreignId('sponsorship_case_id')->nullable()->after('facility_id')->constrained('sponsorship_cases')->nullOnDelete();
            $table->boolean('is_gift')->default(false)->after('notes');
            $table->string('gift_recipient_name')->nullable()->after('is_gift');
            $table->string('gift_recipient_contact')->nullable()->after('gift_recipient_name');
            $table->string('gift_sender_name')->nullable()->after('gift_recipient_contact');
            $table->text('gift_message')->nullable()->after('gift_sender_name');
            $table->string('gift_card_design', 40)->nullable()->after('gift_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sponsorship_case_id');
            $table->dropColumn([
                'is_gift',
                'gift_recipient_name',
                'gift_recipient_contact',
                'gift_sender_name',
                'gift_message',
                'gift_card_design',
            ]);
        });
    }
};
