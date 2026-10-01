<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question_ar');
            $table->string('question_en')->nullable();
            $table->text('answer_ar');
            $table->text('answer_en')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->string('status')->default('published')->index();
            $table->timestamps();
        });

        $this->seedExistingQuestions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }

    /**
     * Carry over the five fixed questions, keeping any text the team already customised in settings.
     */
    private function seedExistingQuestions(): void
    {
        $customised = Schema::hasTable('settings')
            ? DB::table('settings')->where('key', 'like', 'faq_%')->pluck('value', 'key')
            : collect();

        $text = function (string $settingKey, string $translationKey, string $locale) use ($customised): string {
            $custom = trim((string) $customised->get($settingKey, ''));

            return $custom !== '' ? $custom : __($translationKey, [], $locale);
        };

        foreach (['identity', 'politics', 'location', 'work', 'long_term'] as $index => $key) {
            DB::table('faqs')->insert([
                'question_ar' => $text("faq_{$key}_q_ar", "faq.{$key}.question", 'ar'),
                'question_en' => $text("faq_{$key}_q_en", "faq.{$key}.question", 'en'),
                'answer_ar' => $text("faq_{$key}_a_ar", "faq.{$key}.answer", 'ar'),
                'answer_en' => $text("faq_{$key}_a_en", "faq.{$key}.answer", 'en'),
                'order' => $index + 1,
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
