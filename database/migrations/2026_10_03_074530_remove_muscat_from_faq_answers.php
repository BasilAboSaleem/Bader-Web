<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('faqs')
            ->where('answer_ar', 'like', '%مسقط%')
            ->orWhere('answer_en', 'like', '%Muscat%')
            ->update([
                'answer_ar' => __('faq.location.answer', [], 'ar'),
                'answer_en' => __('faq.location.answer', [], 'en'),
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The organisation has no presence outside Gaza; the old wording must not be restored.
    }
};
