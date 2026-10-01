@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit') : __('dashboard.add_new')) . ' — ' . __('dashboard.module.faqs') . ' — ' . __('brand.name'))

@php
  $inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800';
  $textareaClass = 'w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800';
  $labelClass = 'mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400';
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
      {{ $isEdit ? __('dashboard.edit') : __('dashboard.add_new') . ' — ' . __('dashboard.module.faqs') }}
    </h1>
    <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
      <a href="{{ route('dashboard.faqs.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.faqs') }}</a>
      <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      <span>{{ $isEdit ? __('dashboard.edit') : __('dashboard.add_new') }}</span>
    </nav>
  </div>

  @if ($errors->any())
    <div class="rounded-xl border border-error-500/20 bg-error-50 p-4 text-theme-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
      <p class="font-semibold">{{ __('dashboard.fix_errors') }}</p>
      <ul class="mt-1 list-disc ps-5 space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ $isEdit ? route('dashboard.faqs.update', $faq) : route('dashboard.faqs.store') }}" method="POST" class="space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="question_ar" class="{{ $labelClass }}">{{ __('dashboard.question_ar') }} <span class="text-error-500">*</span></label>
          <input type="text" id="question_ar" name="question_ar" value="{{ old('question_ar', $faq->question_ar) }}" required class="{{ $inputClass }}">
        </div>
        <div>
          <label for="question_en" class="{{ $labelClass }}">{{ __('dashboard.question_en') }}</label>
          <input type="text" id="question_en" name="question_en" value="{{ old('question_en', $faq->question_en) }}" dir="ltr" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="answer_ar" class="{{ $labelClass }}">{{ __('dashboard.answer_ar') }} <span class="text-error-500">*</span></label>
          <textarea id="answer_ar" name="answer_ar" rows="6" required class="{{ $textareaClass }}">{{ old('answer_ar', $faq->answer_ar) }}</textarea>
        </div>
        <div>
          <label for="answer_en" class="{{ $labelClass }}">{{ __('dashboard.answer_en') }}</label>
          <textarea id="answer_en" name="answer_en" rows="6" dir="ltr" class="{{ $textareaClass }}">{{ old('answer_en', $faq->answer_en) }}</textarea>
        </div>
        <div>
          <label for="order" class="{{ $labelClass }}">{{ __('dashboard.field.order') }}</label>
          <input type="number" id="order" name="order" value="{{ old('order', $faq->order ?? 0) }}" min="0" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="status" class="{{ $labelClass }}">{{ __('dashboard.field.status') }} <span class="text-error-500">*</span></label>
          <select id="status" name="status" class="{{ $inputClass }}">
            @foreach (['draft', 'published'] as $status)
              <option value="{{ $status }}" @selected(old('status', $faq->status) === $status)>{{ __('dashboard.status.'.$status) }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('dashboard.faqs.index') }}"
        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
        {{ __('dashboard.cancel') }}
      </a>
      <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
        {{ __('dashboard.save_changes') }}
      </button>
    </div>
  </form>
</div>
@endsection
