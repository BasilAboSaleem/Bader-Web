@extends('layouts.public')

@use('App\Support\SiteSettings')
@use('App\Models\ImpactMetric')

@section('title', __('page.'.$key.'.title').' — '.__('brand.name'))
@section('meta_description', SiteSettings::pageIntro($key))

@php
    $sectionIcons = [
        'about.mission' => 'heart',
        'about.work' => 'map-pin',
        'about.independence' => 'shield',
        'impact.verification' => 'check',
        'impact.operations' => 'building',
        'impact.reporting' => 'eye',
        'partners.local' => 'hand-heart',
        'partners.international' => 'globe',
        'partners.community' => 'users',
        'volunteer.field' => 'map-pin',
        'volunteer.skills' => 'sparkle',
        'volunteer.commitment' => 'shield',
    ];
    $hasForm = in_array($key, ['partners', 'volunteer'], true);
    $presidentText = $key === 'about' ? SiteSettings::optionalInstitutional('about', 'president_speech', 'text') : null;
    $visionText = $key === 'about' ? SiteSettings::optionalInstitutional('about', 'vision', 'text') : null;
    $approvedMetrics = $key === 'impact' ? ImpactMetric::approved()->orderBy('order')->get() : collect();
    $exploreLinks = [
        ['route' => 'programs', 'text' => 'about_page.explore_programs'],
        ['route' => 'campaigns', 'text' => 'about_page.explore_campaigns'],
        ['route' => 'sponsorship', 'text' => 'about_page.explore_sponsorship'],
    ];
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('page.'.$key.'.kicker')"
        :title="__('page.'.$key.'.title')"
        :intro="SiteSettings::pageIntro($key)"
    >
        @if ($key === 'about')
            <dl class="mt-6 flex animate-fade-up flex-wrap gap-3 [animation-delay:240ms]">
                <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                    <dt class="text-xs text-subtle">{{ __('about_page.founded') }}</dt>
                    <dd class="text-xl font-extrabold text-forest-700">{{ SiteSettings::foundedYear() }}</dd>
                </div>
                <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                    <dt class="text-xs text-subtle">{{ __('about_page.hq') }}</dt>
                    <dd class="text-xl font-extrabold text-forest-700">{{ SiteSettings::hqLocation() }}</dd>
                </div>
                <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                    <dt class="text-xs text-subtle">{{ __('about_page.field') }}</dt>
                    <dd class="text-xl font-extrabold text-forest-700">{{ SiteSettings::fieldLocation() }}</dd>
                </div>
            </dl>
        @elseif ($hasForm)
            <a href="#{{ $key }}-form" class="btn-brand mt-6 animate-fade-up [animation-delay:240ms]">
                {{ __('form.'.$key.'.heading') }}
                <x-bader.icon name="arrow" class="h-4 w-4" />
            </a>
        @endif
    </x-bader.page-hero>

    @if ($key === 'impact')
        <section class="band-base section-y" aria-labelledby="impact-metrics-title">
            <div class="container-bader">
                <div class="max-w-2xl" data-reveal>
                    <p class="kicker">{{ __('page.impact.kicker') }}</p>
                    <h2 id="impact-metrics-title" class="section-title mt-2">{{ __('page.impact.metrics_title') }}</h2>
                </div>
                @if ($approvedMetrics->isNotEmpty())
                    <dl class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($approvedMetrics as $metric)
                            <div class="surface-card relative overflow-hidden p-6" data-reveal style="animation-delay: {{ $loop->index * 80 }}ms">
                                <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-l from-forest-600 to-gold-500" aria-hidden="true"></span>
                                <dt class="text-sm font-bold text-muted">{{ $metric->title() }}</dt>
                                <dd class="mt-3 flex items-baseline gap-2">
                                    <span class="text-4xl font-extrabold text-forest-700" data-countup="{{ $metric->value }}" dir="ltr">{{ $metric->value }}</span>
                                    @if ($metric->unit())
                                        <span class="text-sm font-bold text-subtle">{{ $metric->unit() }}</span>
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                @else
                    <div class="mt-8 rounded-3xl border border-dashed border-hairline-strong bg-white p-10 text-center" data-reveal>
                        <x-bader.icon name="shield" class="mx-auto h-10 w-10 text-forest-600" />
                        <p class="mx-auto mt-4 max-w-md text-muted">{{ __('page.impact.no_metrics_yet') }}</p>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <section class="{{ $key === 'impact' ? 'band-tint' : 'band-base' }} section-y">
        <div class="container-bader">
            <div class="grid gap-5 md:grid-cols-3">
                @foreach ($sections as $section)
                    <article class="surface-card surface-card-hover relative p-7" data-reveal style="animation-delay: {{ $loop->index * 80 }}ms">
                        <span class="absolute end-6 top-6 text-4xl font-extrabold text-paper-3" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-forest-600/10 text-forest-700">
                            <x-bader.icon :name="$sectionIcons[$section] ?? 'sparkle'" class="h-6 w-6" />
                        </span>
                        <h2 class="mt-5 text-xl font-extrabold text-ink-900">{{ SiteSettings::institutionalTitle($key, $section) }}</h2>
                        <p class="mt-3 text-sm leading-7 text-muted">{{ SiteSettings::institutionalText($key, $section) }}</p>
                    </article>
                @endforeach
            </div>

            @if ($presidentText || $visionText)
                <div @class(['mt-10 grid grid-cols-1 gap-5', 'lg:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]' => $presidentText && $visionText])>
                    @if ($visionText)
                        <article class="relative overflow-hidden rounded-3xl bg-teal-950 p-8 text-white sm:p-10" data-reveal>
                            <span class="pointer-events-none absolute -end-16 -top-16 h-56 w-56 rounded-full bg-gold-500/15 blur-3xl" aria-hidden="true"></span>
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gold-500 text-teal-950">
                                <x-bader.icon name="eye" class="h-6 w-6" />
                            </span>
                            <h2 class="mt-6 text-2xl font-extrabold">{{ SiteSettings::optionalInstitutional('about', 'vision', 'title') ?? __('about_page.vision') }}</h2>
                            <p class="mt-4 whitespace-pre-line text-lg leading-9 text-white/80">{{ $visionText }}</p>
                        </article>
                    @endif

                    @if ($presidentText)
                        <figure class="surface-card relative p-8 sm:p-10" data-reveal style="animation-delay: 80ms">
                            <span class="absolute end-8 top-4 font-serif text-8xl leading-none text-gold-500/40" aria-hidden="true">&rdquo;</span>
                            <p class="kicker">{{ __('about_page.president_kicker') }}</p>
                            <h2 class="mt-2 text-2xl font-extrabold text-ink-900">{{ SiteSettings::optionalInstitutional('about', 'president_speech', 'title') ?? __('about_page.president_speech') }}</h2>
                            <blockquote class="mt-5 whitespace-pre-line border-s-4 border-gold-500 ps-5 text-base leading-8 text-muted">{{ $presidentText }}</blockquote>
                        </figure>
                    @endif
                </div>
            @endif
        </div>
    </section>

    @if ($key === 'about')
        <section class="band-tint section-y" aria-labelledby="about-explore-title">
            <div class="container-bader">
                <div class="max-w-2xl" data-reveal>
                    <p class="kicker">{{ __('about_page.explore_kicker') }}</p>
                    <h2 id="about-explore-title" class="section-title mt-2">{{ __('about_page.explore_title') }}</h2>
                    <p class="section-lead mt-3">{{ __('about_page.explore_text') }}</p>
                </div>
                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    @foreach ($exploreLinks as $link)
                        <a href="{{ route($link['route']) }}" class="surface-card surface-card-hover group flex items-start gap-4 p-6" data-reveal style="animation-delay: {{ $loop->index * 80 }}ms">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-950 text-gold-400">
                                <x-bader.nav-icon :route="$link['route']" class="h-6 w-6" />
                            </span>
                            <span class="min-w-0">
                                <span class="flex items-center gap-2 text-lg font-extrabold text-ink-900 group-hover:text-forest-700">
                                    {{ __('nav.'.$link['route']) }}
                                    <x-bader.icon name="arrow" class="h-4 w-4 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                                </span>
                                <span class="mt-1 block text-sm leading-7 text-muted">{{ __($link['text']) }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($hasForm)
        <section id="{{ $key }}-form" class="band-tint section-y scroll-mt-28" aria-labelledby="{{ $key }}-form-title">
            <div class="container-bader grid grid-cols-1 items-start gap-10 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
                <div class="lg:sticky lg:top-28" data-reveal>
                    <p class="kicker">{{ __('form.'.$key.'.kicker') }}</p>
                    <h2 id="{{ $key }}-form-title" class="section-title mt-2">{{ __('form.'.$key.'.heading') }}</h2>
                    <p class="section-lead mt-3">{{ __('form.'.$key.'.subheading') }}</p>

                    <div class="mt-8 rounded-3xl border border-hairline bg-white p-6">
                        <p class="font-extrabold text-ink-900">{{ __('form_page.next_title') }}</p>
                        <ol class="mt-4 space-y-4">
                            @foreach (['received', 'review', 'privacy'] as $step)
                                <li class="flex gap-3 text-sm leading-7 text-muted">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gold-500 text-xs font-extrabold text-teal-950">{{ $loop->iteration }}</span>
                                    {{ __('form_page.next_'.$step) }}
                                </li>
                            @endforeach
                        </ol>
                        <a href="mailto:{{ SiteSettings::contactEmail() }}" class="mt-6 flex items-center gap-3 border-t border-hairline pt-5 text-sm font-bold text-forest-700 hover:text-forest-800">
                            <x-bader.icon name="mail" class="h-5 w-5" />
                            <span dir="ltr">{{ SiteSettings::contactEmail() }}</span>
                        </a>
                    </div>
                </div>

                <div class="surface-card p-6 sm:p-8" data-reveal>
                    @if (session('success_message'))
                        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-forest-600/20 bg-forest-600/10 p-4 text-sm font-semibold text-forest-800" role="status" data-flash>
                            <x-bader.icon name="check" class="h-5 w-5 shrink-0" />
                            {{ session('success_message') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert" data-flash>
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($key === 'partners')
                        <form method="POST" action="{{ route('partners.submit') }}" class="space-y-4">
                            @csrf
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.contact_person') }} *</span>
                                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" class="field-input">
                                </label>
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.organization') }} *</span>
                                    <input type="text" name="organization" value="{{ old('organization') }}" required autocomplete="organization" class="field-input">
                                </label>
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.email') }} *</span>
                                    <input type="email" name="email" value="{{ old('email') }}" required dir="ltr" autocomplete="email" class="field-input">
                                </label>
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.phone') }} *</span>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" required dir="ltr" autocomplete="tel" class="field-input">
                                </label>
                                <label class="block sm:col-span-2">
                                    <span class="field-label">{{ __('form.field.partnership_type') }}</span>
                                    <input type="text" name="partnership_type" value="{{ old('partnership_type') }}" placeholder="{{ __('form_page.hint_partnership_type') }}" class="field-input">
                                </label>
                            </div>
                            <label class="block">
                                <span class="field-label">{{ __('form.field.proposal_details') }} *</span>
                                <textarea name="message" rows="5" required class="field-input">{{ old('message') }}</textarea>
                            </label>
                            <button type="submit" class="btn-brand min-h-12 w-full px-8 sm:w-auto">
                                {{ __('form.submit_partnership') }}
                                <x-bader.icon name="arrow" class="h-4 w-4" />
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('volunteer.submit') }}" class="space-y-4">
                            @csrf
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.name') }} *</span>
                                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" class="field-input">
                                </label>
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.city_location') }} *</span>
                                    <input type="text" name="location" value="{{ old('location') }}" required class="field-input">
                                </label>
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.email') }} *</span>
                                    <input type="email" name="email" value="{{ old('email') }}" required dir="ltr" autocomplete="email" class="field-input">
                                </label>
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.phone') }} *</span>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" required dir="ltr" autocomplete="tel" class="field-input">
                                </label>
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.skills_specialty') }}</span>
                                    <input type="text" name="skills" value="{{ old('skills') }}" placeholder="{{ __('form_page.hint_skills') }}" class="field-input">
                                </label>
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.availability') }}</span>
                                    <input type="text" name="availability" value="{{ old('availability') }}" placeholder="{{ __('form_page.hint_availability') }}" class="field-input">
                                </label>
                            </div>
                            <label class="block">
                                <span class="field-label">{{ __('form.field.motivation_notes') }}</span>
                                <textarea name="message" rows="4" class="field-input">{{ old('message') }}</textarea>
                            </label>
                            <button type="submit" class="btn-brand min-h-12 w-full px-8 sm:w-auto">
                                {{ __('form.submit_volunteer') }}
                                <x-bader.icon name="arrow" class="h-4 w-4" />
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <x-bader.cta-band band="band-tint" />
@endsection
