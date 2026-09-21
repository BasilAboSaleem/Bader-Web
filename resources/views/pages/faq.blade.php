@extends('layouts.public')

@section('title', __('page.faq.title').' — '.__('brand.name'))

@php
    use App\Support\SiteSettings;
@endphp

@section('content')
    <section class="bg-bader-green-deep text-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
            <x-bader.section-heading :kicker="__('page.faq.kicker')" :title="__('page.faq.title')" theme="dark" />
            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base">{{ SiteSettings::pageIntro('faq') }}</p>
        </div>
    </section>

    <section class="bg-bader-paper">
        <div class="mx-auto max-w-4xl space-y-3 px-4 py-14">
            @foreach ($questions as $question)
                <details class="group rounded-2xl border border-bader-green/10 bg-white p-5" data-reveal>
                    <summary class="flex cursor-pointer items-center justify-between gap-4 text-base font-semibold text-bader-green">
                        {{ SiteSettings::faqQuestion($question) }}
                        <span class="text-bader-gold transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-4 max-w-3xl text-sm leading-relaxed text-bader-ink/70">{{ SiteSettings::faqAnswer($question) }}</p>
                </details>
            @endforeach
        </div>
    </section>
@endsection
