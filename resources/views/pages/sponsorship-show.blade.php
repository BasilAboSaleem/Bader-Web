@extends('layouts.public')

@use('App\Support\Money')

@section('title', $case->name.' — '.__('nav.sponsorship').' — '.__('brand.name'))
@section('meta_description', \Illuminate\Support\Str::limit((string) ($case->bio ?: __('sponsorship_page.intro')), 160))

@section('content')
    <x-bader.page-hero
        :kicker="$case->type_label"
        :title="$case->name"
        :breadcrumbs="[['label' => __('nav.sponsorship'), 'url' => route('sponsorship')]]"
    />

    <section class="band-base section-y">
        <div class="container-bader grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="min-w-0 space-y-6">
                <div class="surface-card flex flex-col gap-6 p-6 sm:flex-row sm:items-center sm:p-8" data-reveal>
                    <div class="h-36 w-36 shrink-0 overflow-hidden rounded-3xl bg-paper-3">
                        @if ($case->photo)
                            <img src="{{ asset($case->photo) }}" alt="{{ $case->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="flex h-full w-full items-center justify-center text-forest-600">
                                <x-bader.icon name="users" class="h-14 w-14" />
                            </span>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-forest-600/10 px-3 py-1 text-xs font-extrabold text-forest-700">{{ $case->type_label }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $case->isAvailable() ? 'bg-gold-500 text-teal-950' : 'bg-paper-2 text-forest-700' }}">{{ __('sponsorship.status.'.$case->status) }}</span>
                        </div>
                        <dl class="mt-5 grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                            <div>
                                <dt class="text-xs text-subtle">{{ __('sponsorship_page.case_code') }}</dt>
                                <dd class="mt-0.5 font-extrabold text-ink-900" dir="ltr">#{{ $case->code }}</dd>
                            </div>
                            @if ($case->age)
                                <div>
                                    <dt class="text-xs text-subtle">{{ __('sponsorship_page.age') }}</dt>
                                    <dd class="mt-0.5 font-extrabold text-ink-900">{{ __('sponsorship.age_years', ['age' => $case->age]) }}</dd>
                                </div>
                            @endif
                            @if ($case->gender)
                                <div>
                                    <dt class="text-xs text-subtle">{{ __('sponsorship_page.gender') }}</dt>
                                    <dd class="mt-0.5 font-extrabold text-ink-900">{{ __('sponsorship.gender.'.$case->gender) }}</dd>
                                </div>
                            @endif
                            @if ($case->region)
                                <div>
                                    <dt class="text-xs text-subtle">{{ __('campaign_page.region') }}</dt>
                                    <dd class="mt-0.5 font-extrabold text-ink-900">
                                        <a href="{{ route('regions.show', $case->region->key) }}" class="hover:text-forest-700">{{ $case->region->name }}</a>
                                    </dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-xs text-subtle">{{ __('sponsorship_page.monthly_amount') }}</dt>
                                <dd class="mt-0.5 font-extrabold text-ink-900" dir="ltr">{{ Money::format($case->monthly_amount) }}</dd>
                            </div>
                            @if ($case->duration_months)
                                <div>
                                    <dt class="text-xs text-subtle">{{ __('sponsorship_page.duration') }}</dt>
                                    <dd class="mt-0.5 font-extrabold text-ink-900">{{ trans_choice('sponsorship_page.months', $case->duration_months, ['count' => $case->duration_months]) }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                @if ($case->bio)
                    <div class="surface-card p-6 sm:p-8" data-reveal>
                        <h2 class="text-xl font-extrabold text-ink-900">{{ __('sponsorship_page.about_case') }}</h2>
                        <p class="mt-3 leading-8 text-muted">{{ $case->bio }}</p>
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-3" data-reveal>
                    <div class="rounded-2xl bg-paper-2 p-5">
                        <p class="text-xs text-subtle">{{ __('sponsorship_page.yearly_amount') }}</p>
                        <p class="mt-1 text-xl font-extrabold text-ink-900" dir="ltr">{{ Money::format($case->yearly_amount) }}</p>
                    </div>
                    @if ($case->isAvailable() && $case->waiting_since)
                        <div class="rounded-2xl bg-paper-2 p-5 sm:col-span-2">
                            <p class="text-xs text-subtle">{{ __('sponsorship_page.waiting') }}</p>
                            <p class="mt-1 text-xl font-extrabold text-ink-900">{{ $case->waiting_since->diffForHumans(syntax: \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}</p>
                        </div>
                    @endif
                </div>

                <p class="flex items-start gap-3 rounded-2xl border border-hairline bg-white p-5 text-sm leading-relaxed text-muted" data-reveal>
                    <x-bader.icon name="lock" class="mt-0.5 h-5 w-5 shrink-0 text-forest-600" />
                    {{ __('sponsorship_page.privacy_note') }}
                </p>
            </div>

            <aside class="lg:sticky lg:top-28" aria-label="{{ __('donate_box.kicker') }}">
                @if ($case->isAvailable())
                    <x-bader.donate-box
                        :title="__('sponsorship_page.sponsor_title', ['name' => $case->name])"
                        :donate-params="['target_type' => 'sponsorship', 'target_id' => $case->id]"
                        :amounts="[$case->monthly_amount + 0, $case->monthly_amount * 3, $case->yearly_amount]"
                        frequency="monthly"
                    >
                        <p class="text-sm leading-relaxed text-muted">{{ __('sponsorship_page.donate_note', ['amount' => Money::format($case->monthly_amount)]) }}</p>
                    </x-bader.donate-box>
                @else
                    <div class="surface-card p-6 text-center">
                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-forest-600/10 text-forest-700">
                            <x-bader.icon name="check" class="h-7 w-7" />
                        </span>
                        <p class="mt-4 text-lg font-extrabold text-ink-900">{{ __('sponsorship_page.already_sponsored') }}</p>
                        <a href="{{ route('sponsorship') }}" class="btn-brand mt-5">{{ __('header.all_cases') }}</a>
                    </div>
                @endif
            </aside>
        </div>
    </section>

    @if ($otherCases->isNotEmpty())
        <section class="band-tint section-y">
            <div class="container-bader">
                <h2 class="section-title" data-reveal>{{ __('sponsorship_page.other_cases') }}</h2>
                <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($otherCases as $otherCase)
                        <x-bader.sponsorship-case-card :case="$otherCase" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
