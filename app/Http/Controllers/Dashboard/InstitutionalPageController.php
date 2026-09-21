<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionalPageController extends Controller
{
    /**
     * Display the institutional pages management form.
     */
    public function edit(): View
    {
        $aboutSections = ['mission', 'work', 'independence'];
        $faqKeys = ['identity', 'politics', 'location', 'work', 'long_term'];
        $policySections = ['verification', 'operations', 'reporting'];

        $aboutData = [
            'intro_ar' => SiteSettings::pageIntro('about', 'ar'),
            'intro_en' => SiteSettings::pageIntro('about', 'en'),
            'president_title_ar' => SiteSettings::institutionalTitle('about', 'president_speech', 'ar'),
            'president_title_en' => SiteSettings::institutionalTitle('about', 'president_speech', 'en'),
            'president_text_ar' => SiteSettings::institutionalText('about', 'president_speech', 'ar'),
            'president_text_en' => SiteSettings::institutionalText('about', 'president_speech', 'en'),
            'vision_title_ar' => SiteSettings::institutionalTitle('about', 'vision', 'ar'),
            'vision_title_en' => SiteSettings::institutionalTitle('about', 'vision', 'en'),
            'vision_text_ar' => SiteSettings::institutionalText('about', 'vision', 'ar'),
            'vision_text_en' => SiteSettings::institutionalText('about', 'vision', 'en'),
            'sections' => [],
        ];

        foreach ($aboutSections as $sec) {
            $aboutData['sections'][$sec] = [
                'title_ar' => SiteSettings::institutionalTitle('about', $sec, 'ar'),
                'title_en' => SiteSettings::institutionalTitle('about', $sec, 'en'),
                'text_ar' => SiteSettings::institutionalText('about', $sec, 'ar'),
                'text_en' => SiteSettings::institutionalText('about', $sec, 'en'),
            ];
        }

        $policiesData = [
            'intro_ar' => SiteSettings::pageIntro('impact', 'ar'),
            'intro_en' => SiteSettings::pageIntro('impact', 'en'),
            'sections' => [],
        ];

        foreach ($policySections as $sec) {
            $policiesData['sections'][$sec] = [
                'title_ar' => SiteSettings::institutionalTitle('impact', $sec, 'ar'),
                'title_en' => SiteSettings::institutionalTitle('impact', $sec, 'en'),
                'text_ar' => SiteSettings::institutionalText('impact', $sec, 'ar'),
                'text_en' => SiteSettings::institutionalText('impact', $sec, 'en'),
            ];
        }

        $faqsData = [];
        foreach ($faqKeys as $key) {
            $faqsData[$key] = [
                'question_ar' => SiteSettings::faqQuestion($key, 'ar'),
                'question_en' => SiteSettings::faqQuestion($key, 'en'),
                'answer_ar' => SiteSettings::faqAnswer($key, 'ar'),
                'answer_en' => SiteSettings::faqAnswer($key, 'en'),
            ];
        }

        return view('dashboard.pages.edit', compact('aboutData', 'policiesData', 'faqsData', 'aboutSections', 'policySections', 'faqKeys'));
    }

    /**
     * Update institutional pages content.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'inst_about_intro_ar' => ['nullable', 'string'],
            'inst_about_intro_en' => ['nullable', 'string'],
            'inst_about_president_speech_title_ar' => ['nullable', 'string', 'max:255'],
            'inst_about_president_speech_title_en' => ['nullable', 'string', 'max:255'],
            'inst_about_president_speech_text_ar' => ['nullable', 'string'],
            'inst_about_president_speech_text_en' => ['nullable', 'string'],
            'inst_about_vision_title_ar' => ['nullable', 'string', 'max:255'],
            'inst_about_vision_title_en' => ['nullable', 'string', 'max:255'],
            'inst_about_vision_text_ar' => ['nullable', 'string'],
            'inst_about_vision_text_en' => ['nullable', 'string'],

            'inst_about_mission_title_ar' => ['nullable', 'string', 'max:255'],
            'inst_about_mission_title_en' => ['nullable', 'string', 'max:255'],
            'inst_about_mission_text_ar' => ['nullable', 'string'],
            'inst_about_mission_text_en' => ['nullable', 'string'],

            'inst_about_work_title_ar' => ['nullable', 'string', 'max:255'],
            'inst_about_work_title_en' => ['nullable', 'string', 'max:255'],
            'inst_about_work_text_ar' => ['nullable', 'string'],
            'inst_about_work_text_en' => ['nullable', 'string'],

            'inst_about_independence_title_ar' => ['nullable', 'string', 'max:255'],
            'inst_about_independence_title_en' => ['nullable', 'string', 'max:255'],
            'inst_about_independence_text_ar' => ['nullable', 'string'],
            'inst_about_independence_text_en' => ['nullable', 'string'],

            'inst_impact_intro_ar' => ['nullable', 'string'],
            'inst_impact_intro_en' => ['nullable', 'string'],
            'inst_impact_verification_title_ar' => ['nullable', 'string', 'max:255'],
            'inst_impact_verification_title_en' => ['nullable', 'string', 'max:255'],
            'inst_impact_verification_text_ar' => ['nullable', 'string'],
            'inst_impact_verification_text_en' => ['nullable', 'string'],
            'inst_impact_operations_title_ar' => ['nullable', 'string', 'max:255'],
            'inst_impact_operations_title_en' => ['nullable', 'string', 'max:255'],
            'inst_impact_operations_text_ar' => ['nullable', 'string'],
            'inst_impact_operations_text_en' => ['nullable', 'string'],
            'inst_impact_reporting_title_ar' => ['nullable', 'string', 'max:255'],
            'inst_impact_reporting_title_en' => ['nullable', 'string', 'max:255'],
            'inst_impact_reporting_text_ar' => ['nullable', 'string'],
            'inst_impact_reporting_text_en' => ['nullable', 'string'],

            'faq_*_q_ar' => ['nullable', 'string', 'max:255'],
            'faq_*_q_en' => ['nullable', 'string', 'max:255'],
            'faq_*_a_ar' => ['nullable', 'string'],
            'faq_*_a_en' => ['nullable', 'string'],
        ]);

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'inst_') || str_starts_with($key, 'faq_')) {
                Setting::set($key, (string) ($value ?? ''), 'pages');
            }
        }

        return redirect()->route('dashboard.pages.edit')
            ->with('status', __('dashboard.pages_saved'));
    }
}
