@extends('layouts.dashboard')

@section('title', __('dashboard.module.pages') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  {{-- Page Header / Breadcrumb --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
        {{ __('dashboard.pages_title') }}
      </h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
        {{ __('dashboard.pages_subtitle') }}
      </p>
    </div>
    <div class="flex items-center gap-3">
      <a
        href="{{ route('about') }}"
        target="_blank"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
      >
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        <span>{{ __('dashboard.view_about_page') }}</span>
      </a>
    </div>
  </div>

  {{-- Flash message --}}
  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  <form action="{{ route('dashboard.pages.update') }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- 1. About Bader Page (عن بادر ورؤيتها ورسالتها) --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
        <div>
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ __('dashboard.section_about_title') }}
          </h2>
          <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">
            {{ __('dashboard.section_about_desc') }}
          </p>
        </div>
      </div>

      <div class="space-y-5">
        {{-- Intro Texts --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <div>
            <label for="inst_about_intro_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
              {{ __('dashboard.field_about_intro_ar') }}
            </label>
            <textarea
              id="inst_about_intro_ar"
              name="inst_about_intro_ar"
              rows="3"
              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            >{{ old('inst_about_intro_ar', $aboutData['intro_ar']) }}</textarea>
          </div>
          <div>
            <label for="inst_about_intro_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
              {{ __('dashboard.field_about_intro_en') }}
            </label>
            <textarea
              id="inst_about_intro_en"
              name="inst_about_intro_en"
              rows="3"
              dir="ltr"
              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            >{{ old('inst_about_intro_en', $aboutData['intro_en']) }}</textarea>
          </div>
        </div>

        {{-- Dynamic Pillars / Sections: Mission, Work, Independence --}}
        @foreach ($aboutSections as $sec)
          <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-900/40">
            <h3 class="mb-3 text-theme-sm font-semibold text-gray-800 dark:text-white/90">
              {{ __('dashboard.pillar_'.$sec) }}
            </h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.title_ar') }}</label>
                <input
                  type="text"
                  name="inst_about_{{ $sec }}_title_ar"
                  value="{{ old('inst_about_'.$sec.'_title_ar', $aboutData['sections'][$sec]['title_ar']) }}"
                  class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                <label class="mb-1 mt-2.5 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.text_ar') }}</label>
                <textarea
                  name="inst_about_{{ $sec }}_text_ar"
                  rows="2"
                  class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >{{ old('inst_about_'.$sec.'_text_ar', $aboutData['sections'][$sec]['text_ar']) }}</textarea>
              </div>

              <div>
                <label class="mb-1 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.title_en') }}</label>
                <input
                  type="text"
                  name="inst_about_{{ $sec }}_title_en"
                  value="{{ old('inst_about_'.$sec.'_title_en', $aboutData['sections'][$sec]['title_en']) }}"
                  dir="ltr"
                  class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                <label class="mb-1 mt-2.5 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.text_en') }}</label>
                <textarea
                  name="inst_about_{{ $sec }}_text_en"
                  rows="2"
                  dir="ltr"
                  class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >{{ old('inst_about_'.$sec.'_text_en', $aboutData['sections'][$sec]['text_en']) }}</textarea>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- 2. Impact & Governance / Policies (السياسات والشفافية) --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
        <div>
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ __('dashboard.section_policies_title') }}
          </h2>
          <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">
            {{ __('dashboard.section_policies_desc') }}
          </p>
        </div>
      </div>

      <div class="space-y-5">
        @foreach ($policySections as $sec)
          <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-900/40">
            <h3 class="mb-3 text-theme-sm font-semibold text-gray-800 dark:text-white/90">
              {{ __('dashboard.policy_'.$sec) }}
            </h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.title_ar') }}</label>
                <input
                  type="text"
                  name="inst_impact_{{ $sec }}_title_ar"
                  value="{{ old('inst_impact_'.$sec.'_title_ar', $policiesData['sections'][$sec]['title_ar']) }}"
                  class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                <label class="mb-1 mt-2.5 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.text_ar') }}</label>
                <textarea
                  name="inst_impact_{{ $sec }}_text_ar"
                  rows="2"
                  class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >{{ old('inst_impact_'.$sec.'_text_ar', $policiesData['sections'][$sec]['text_ar']) }}</textarea>
              </div>

              <div>
                <label class="mb-1 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.title_en') }}</label>
                <input
                  type="text"
                  name="inst_impact_{{ $sec }}_title_en"
                  value="{{ old('inst_impact_'.$sec.'_title_en', $policiesData['sections'][$sec]['title_en']) }}"
                  dir="ltr"
                  class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                <label class="mb-1 mt-2.5 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.text_en') }}</label>
                <textarea
                  name="inst_impact_{{ $sec }}_text_en"
                  rows="2"
                  dir="ltr"
                  class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >{{ old('inst_impact_'.$sec.'_text_en', $policiesData['sections'][$sec]['text_en']) }}</textarea>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- 3. Frequently Asked Questions (الأسئلة الشائعة) --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
        <div>
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ __('dashboard.section_faq_title') }}
          </h2>
          <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">
            {{ __('dashboard.section_faq_desc') }}
          </p>
        </div>
      </div>

      <div class="space-y-5">
        @foreach ($faqKeys as $key)
          <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-900/40">
            <h3 class="mb-3 text-theme-sm font-semibold text-gray-800 dark:text-white/90">
              {{ __('dashboard.faq_item_'.$key) }}
            </h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.question_ar') }}</label>
                <input
                  type="text"
                  name="faq_{{ $key }}_q_ar"
                  value="{{ old('faq_'.$key.'_q_ar', $faqsData[$key]['question_ar']) }}"
                  class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                <label class="mb-1 mt-2.5 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.answer_ar') }}</label>
                <textarea
                  name="faq_{{ $key }}_a_ar"
                  rows="2"
                  class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >{{ old('faq_'.$key.'_a_ar', $faqsData[$key]['answer_ar']) }}</textarea>
              </div>

              <div>
                <label class="mb-1 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.question_en') }}</label>
                <input
                  type="text"
                  name="faq_{{ $key }}_q_en"
                  value="{{ old('faq_'.$key.'_q_en', $faqsData[$key]['question_en']) }}"
                  dir="ltr"
                  class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                <label class="mb-1 mt-2.5 block text-theme-xs font-medium text-gray-600 dark:text-gray-400">{{ __('dashboard.answer_en') }}</label>
                <textarea
                  name="faq_{{ $key }}_a_en"
                  rows="2"
                  dir="ltr"
                  class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >{{ old('faq_'.$key.'_a_en', $faqsData[$key]['answer_en']) }}</textarea>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Form Submit Actions --}}
    <div class="flex items-center justify-end gap-3 pt-2">
      <a
        href="{{ route('dashboard') }}"
        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
      >
        {{ __('dashboard.cancel') }}
      </a>
      <button
        type="submit"
        class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition"
      >
        <svg class="me-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ __('dashboard.save_changes') }}</span>
      </button>
    </div>

  </form>
</div>
@endsection
