@extends('layouts.public')

@use('App\Support\Money')

@section('title', __('sponsorship_page.title').' — '.__('brand.name'))
@section('meta_description', __('sponsorship_page.intro'))

@php
    $filterUrl = fn (array $overrides) => route('sponsorship', array_filter(array_merge([
        'type' => $selectedType,
        'region' => $selectedRegion?->key,
    ], $overrides)));
    $steps = ['choose', 'sponsor', 'follow', 'impact'];
    $questions = ['who', 'amount', 'reports', 'stop'];
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('nav.sponsorship')"
        :title="__('sponsorship_page.title')"
        :intro="__('sponsorship_page.intro')"
    >
        <dl class="mt-6 flex flex-wrap gap-3">
            <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                <dt class="text-xs text-subtle">{{ __('sponsorship_page.stat_waiting') }}</dt>
                <dd class="text-2xl font-extrabold text-forest-700" data-countup="{{ $stats['waiting'] }}">{{ $stats['waiting'] }}</dd>
            </div>
            <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                <dt class="text-xs text-subtle">{{ __('sponsorship_page.stat_sponsored') }}</dt>
                <dd class="text-2xl font-extrabold text-forest-700" data-countup="{{ $stats['sponsored'] }}">{{ $stats['sponsored'] }}</dd>
            </div>
            @if ($stats['from'])
                <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                    <dt class="text-xs text-subtle">{{ __('sponsorship_page.stat_from') }}</dt>
                    <dd class="text-2xl font-extrabold text-forest-700" dir="ltr">{{ Money::format($stats['from']) }}</dd>
                </div>
            @endif
        </dl>
    </x-bader.page-hero>

    <section class="band-base section-y" aria-labelledby="sponsorship-cases-title">
        <div class="container-bader">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div data-reveal>
                    <p class="kicker">{{ __('header.sponsorship_title') }}</p>
                    <h2 id="sponsorship-cases-title" class="section-title mt-2">{{ __('sponsorship_page.cases_title') }}</h2>
                    <p class="section-lead mt-2">{{ __('sponsorship_page.cases_intro') }}</p>
                </div>
            </div>

            <div class="mt-6 space-y-3" data-reveal>
                <nav aria-label="{{ __('sponsorship_page.filter_type') }}" class="-mx-4 overflow-x-auto px-4 [scrollbar-width:none]">
                    <ul class="flex w-max gap-2">
                        <li><a href="{{ $filterUrl(['type' => null]) }}" class="chip {{ $selectedType ? '' : 'is-active' }}" @unless ($selectedType) aria-current="true" @endunless>{{ __('sponsorship_page.all_types') }}</a></li>
                        @foreach (\App\Models\SponsorshipCase::TYPES as $type)
                            <li><a href="{{ $filterUrl(['type' => $type]) }}" class="chip {{ $selectedType === $type ? 'is-active' : '' }}" @if ($selectedType === $type) aria-current="true" @endif>{{ __('sponsorship.type.'.$type) }}</a></li>
                        @endforeach
                    </ul>
                </nav>
                @if ($regions->isNotEmpty())
                    <nav aria-label="{{ __('campaigns_page.filter_region') }}" class="-mx-4 overflow-x-auto px-4 [scrollbar-width:none]">
                        <ul class="flex w-max gap-2">
                            <li><a href="{{ $filterUrl(['region' => null]) }}" class="chip {{ $selectedRegion ? '' : 'is-active' }}" @unless ($selectedRegion) aria-current="true" @endunless>{{ __('campaigns_page.all_regions') }}</a></li>
                            @foreach ($regions as $region)
                                @php $isSelected = $selectedRegion?->is($region); @endphp
                                <li>
                                    <a href="{{ $filterUrl(['region' => $region->key]) }}" class="chip whitespace-nowrap {{ $isSelected ? 'is-active' : '' }}" @if ($isSelected) aria-current="true" @endif>
                                        <x-bader.icon name="map-pin" class="h-3.5 w-3.5" />
                                        {{ $region->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endif
            </div>

            @if ($cases->isNotEmpty())
                <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($cases as $case)
                        <x-bader.sponsorship-case-card :case="$case" />
                    @endforeach
                </div>
                @if ($cases->hasPages())
                    <div class="mt-10">{{ $cases->links() }}</div>
                @endif
            @else
                <div class="mt-8 rounded-3xl border border-dashed border-hairline-strong bg-white p-10 text-center">
                    <x-bader.icon name="users" class="mx-auto h-10 w-10 text-forest-600" />
                    <p class="mt-4 text-muted">{{ __('sponsorship_page.empty') }}</p>
                    <a href="#sponsorship-request" class="btn-brand mt-6">{{ __('sponsorship_page.request_cta') }}</a>
                </div>
            @endif
        </div>
    </section>

    <section id="sponsorship-how" class="band-tint section-y scroll-mt-28" aria-labelledby="sponsorship-how-title">
        <div class="container-bader">
            <div class="max-w-2xl" data-reveal>
                <p class="kicker">{{ __('sponsorship_page.how_kicker') }}</p>
                <h2 id="sponsorship-how-title" class="section-title mt-2">{{ __('home.sponsorship.how') }}</h2>
            </div>
            <ol class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as $step)
                    <li class="surface-card relative p-6" data-reveal style="animation-delay: {{ $loop->index * 80 }}ms">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gold-500 text-lg font-extrabold text-teal-950">{{ $loop->iteration }}</span>
                        <h3 class="mt-4 text-lg font-extrabold text-ink-900">{{ __('sponsorship_page.step_'.$step.'_title') }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ __('sponsorship_page.step_'.$step.'_text') }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="band-base section-y" aria-labelledby="sponsorship-faq-title">
        <div class="container-bader grid gap-10 lg:grid-cols-2">
            <div data-reveal>
                <p class="kicker">{{ __('sponsorship_page.faq_kicker') }}</p>
                <h2 id="sponsorship-faq-title" class="section-title mt-2">{{ __('sponsorship_page.faq_title') }}</h2>
                <div class="mt-6 divide-y divide-hairline rounded-3xl border border-hairline bg-white">
                    @foreach ($questions as $question)
                        <details class="group p-5" @if ($loop->first) open @endif>
                            <summary class="flex cursor-pointer items-center justify-between gap-4 font-extrabold text-ink-900">
                                {{ __('sponsorship_page.faq_'.$question.'_q') }}
                                <x-bader.icon name="chevron-down" class="h-4 w-4 shrink-0 transition group-open:rotate-180" />
                            </summary>
                            <p class="mt-3 text-sm leading-relaxed text-muted">{{ __('sponsorship_page.faq_'.$question.'_a') }}</p>
                        </details>
                    @endforeach
                </div>
            </div>

            <div id="sponsorship-request" class="scroll-mt-28" data-reveal>
                <div class="surface-card p-6 sm:p-8">
                    <p class="kicker">{{ __('form.sponsorship.kicker') }}</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-ink-900">{{ __('form.sponsorship.heading') }}</h2>
                    <p class="mt-2 text-sm text-muted">{{ __('form.sponsorship.subheading') }}</p>

                    @if (session('success_message'))
                        <div class="mt-5 rounded-2xl border border-forest-600/20 bg-forest-600/10 p-4 text-sm font-semibold text-forest-800" role="status" data-flash>{{ session('success_message') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert" data-flash>
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('sponsorship.submit') }}" class="mt-6 space-y-4">
                        @csrf
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="field-label">{{ __('form.field.sponsor_name') }} *</span>
                                <input type="text" name="name" value="{{ old('name') }}" required class="field-input">
                            </label>
                            <label class="block">
                                <span class="field-label">{{ __('form.field.sponsorship_type') }} *</span>
                                <select name="sponsorship_type" required class="field-input">
                                    @foreach (['orphan', 'widow', 'family', 'student'] as $type)
                                        <option value="{{ $type }}" @selected(old('sponsorship_type') === $type)>{{ __('form.sponsorship_type_'.$type) }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="block">
                                <span class="field-label">{{ __('form.field.email') }} *</span>
                                <input type="email" name="email" value="{{ old('email') }}" required dir="ltr" class="field-input">
                            </label>
                            <label class="block">
                                <span class="field-label">{{ __('form.field.phone') }} *</span>
                                <input type="text" name="phone" value="{{ old('phone') }}" required dir="ltr" class="field-input">
                            </label>
                            <label class="block sm:col-span-2">
                                <span class="field-label">{{ __('form.field.beneficiaries_count') }}</span>
                                <input type="number" min="1" max="50" name="beneficiaries_count" value="{{ old('beneficiaries_count', 1) }}" class="field-input">
                            </label>
                        </div>
                        <label class="block">
                            <span class="field-label">{{ __('form.field.preferences_notes') }}</span>
                            <textarea name="message" rows="3" class="field-input">{{ old('message') }}</textarea>
                        </label>
                        <button type="submit" class="btn-brand w-full sm:w-auto">{{ __('form.submit_sponsorship') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
