<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Phrases stating the founding year, rewritten to follow the year saved in Site Settings.
     *
     * @var array<string, string>
     */
    private const FOUNDED_PHRASES = [
        'تأسست عام 2023' => 'تأسست عام :founded_year',
        'founded in Gaza in 2023' => 'founded in Gaza in :founded_year',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (DB::table('faqs')->get(['id', 'answer_ar', 'answer_en']) as $faq) {
            DB::table('faqs')->where('id', $faq->id)->update([
                'answer_ar' => $this->withPlaceholder($faq->answer_ar),
                'answer_en' => $this->withPlaceholder($faq->answer_en),
            ]);
        }

        foreach (DB::table('settings')->where('key', 'like', 'inst_%_intro_%')->get(['id', 'value']) as $setting) {
            DB::table('settings')->where('id', $setting->id)->update(['value' => $this->withPlaceholder($setting->value)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (DB::table('faqs')->get(['id', 'answer_ar', 'answer_en']) as $faq) {
            DB::table('faqs')->where('id', $faq->id)->update([
                'answer_ar' => str_replace(array_values(self::FOUNDED_PHRASES), array_keys(self::FOUNDED_PHRASES), (string) $faq->answer_ar),
                'answer_en' => str_replace(array_values(self::FOUNDED_PHRASES), array_keys(self::FOUNDED_PHRASES), (string) $faq->answer_en),
            ]);
        }
    }

    private function withPlaceholder(?string $text): ?string
    {
        return $text === null ? null : str_replace(array_keys(self::FOUNDED_PHRASES), array_values(self::FOUNDED_PHRASES), $text);
    }
};
