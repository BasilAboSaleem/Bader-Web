@extends('layouts.public')

@use('App\Support\Money')

@section('title', __('zakat_page.title').' — '.__('brand.name'))
@section('meta_description', __('zakat_page.intro'))

@php
    $moneyFields = ['cash', 'savings', 'trade', 'debts'];
    $facts = ['rate', 'nisab', 'hawl', 'recipients'];
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('nav.zakat')"
        :title="__('zakat_page.title')"
        :intro="__('zakat_page.intro')"
    />

    <section class="band-base section-y">
        <div class="container-bader grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_24rem]"
            data-zakat
            data-mode="both"
            data-gold-price="{{ $goldPricePerGram }}"
            data-donate-base="{{ route('donate') }}">
            <div class="min-w-0 space-y-6">
                <div class="surface-card p-6 sm:p-8" data-reveal>
                    <p class="field-label">{{ __('zakat_page.mode_label') }}</p>
                    <div class="segmented" role="group" aria-label="{{ __('zakat_page.mode_label') }}">
                        @foreach (['both', 'money', 'gold'] as $mode)
                            <button type="button" data-zakat-mode data-value="{{ $mode }}" aria-pressed="{{ $mode === 'both' ? 'true' : 'false' }}">{{ __('zakat_page.mode_'.$mode) }}</button>
                        @endforeach
                    </div>
                </div>

                <fieldset class="surface-card p-6 sm:p-8" data-zakat-section="money" data-reveal>
                    <legend class="sr-only">{{ __('zakat_page.money_title') }}</legend>
                    <p class="flex items-center gap-2 text-lg font-extrabold text-ink-900">
                        <x-bader.icon name="bank" class="h-5 w-5 text-forest-600" />
                        {{ __('zakat_page.money_title') }}
                    </p>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        @foreach ($moneyFields as $field)
                            <label class="block">
                                <span class="field-label">{{ __('zakat.field.'.$field) }}</span>
                                <span class="relative block">
                                    <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-sm font-bold text-subtle">$</span>
                                    <input type="number" min="0" step="any" inputmode="decimal" class="field-input ps-7" placeholder="0" data-zakat-input="{{ $field }}">
                                </span>
                                @if ($field === 'debts')
                                    <span class="mt-1 block text-xs text-subtle">{{ __('zakat_page.debts_hint') }}</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset class="surface-card p-6 sm:p-8" data-zakat-section="gold" data-reveal>
                    <legend class="sr-only">{{ __('zakat_page.gold_title') }}</legend>
                    <p class="flex items-center gap-2 text-lg font-extrabold text-ink-900">
                        <x-bader.icon name="sparkle" class="h-5 w-5 text-forest-600" />
                        {{ __('zakat_page.gold_title') }}
                    </p>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="field-label">{{ __('zakat.field.gold_grams') }}</span>
                            <input type="number" min="0" step="any" inputmode="decimal" class="field-input" placeholder="0" data-zakat-input="gold_grams">
                        </label>
                        <label class="block">
                            <span class="field-label">{{ __('zakat.field.gold_karat') }}</span>
                            <select class="field-input" data-zakat-input="gold_karat">
                                @foreach ([24, 22, 21, 18] as $karat)
                                    <option value="{{ $karat }}">{{ __('zakat_page.karat', ['karat' => $karat]) }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <p class="mt-4 text-xs text-subtle">{{ __('zakat_page.gold_price_note', ['price' => Money::format($goldPricePerGram)]) }}</p>
                </fieldset>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-28" aria-live="polite">
                <div class="rounded-3xl bg-teal-950 p-6 text-white shadow-card-lg sm:p-7">
                    <p class="inline-flex items-center gap-2 text-xs font-extrabold text-gold-400">
                        <x-bader.icon name="calculator" class="h-4 w-4" />
                        {{ __('zakat_page.result_title') }}
                    </p>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between gap-3">
                            <dt class="text-white/70">{{ __('zakat_page.total_wealth') }}</dt>
                            <dd class="font-bold" dir="ltr">$<span data-zakat-output="wealth">0</span></dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-white/70">{{ __('zakat.nisab') }} ({{ __('zakat_page.nisab_grams', ['grams' => $nisabGrams]) }})</dt>
                            <dd class="font-bold" dir="ltr">$<span data-zakat-output="nisab">{{ number_format($goldPricePerGram * $nisabGrams) }}</span></dd>
                        </div>
                    </dl>
                    <div class="mt-5 rounded-2xl bg-white/10 p-5 text-center">
                        <p class="text-sm text-white/75">{{ __('zakat.due') }}</p>
                        <p class="mt-1 text-4xl font-extrabold text-gold-400" dir="ltr">$<span data-zakat-output="due">0</span></p>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gold-300" data-zakat-status="below" hidden>{{ __('zakat.below_nisab') }}</p>
                    <p class="mt-4 text-sm font-semibold text-gold-300" data-zakat-status="above" hidden>{{ __('zakat_page.above_nisab') }}</p>
                    <a href="{{ route('donate', ['category' => 'zakat']) }}" class="btn-primary mt-5 min-h-12 w-full text-base" data-zakat-pay>
                        <x-bader.icon name="heart" class="h-5 w-5" />
                        {{ __('zakat.pay') }}
                    </a>
                </div>
                <p class="flex items-start gap-2 text-xs leading-relaxed text-subtle">
                    <x-bader.icon name="info" class="mt-0.5 h-4 w-4 shrink-0" />
                    {{ __('zakat_page.disclaimer') }}
                </p>
            </aside>
        </div>
    </section>

    <section class="band-tint section-y">
        <div class="container-bader">
            <h2 class="section-title" data-reveal>{{ __('zakat_page.facts_title') }}</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($facts as $fact)
                    <div class="surface-card p-6" data-reveal style="animation-delay: {{ $loop->index * 70 }}ms">
                        <h3 class="text-lg font-extrabold text-ink-900">{{ __('zakat_page.fact_'.$fact.'_title') }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ __('zakat_page.fact_'.$fact.'_text', ['grams' => $nisabGrams]) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
