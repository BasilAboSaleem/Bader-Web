@extends('layouts.public')

@section('title', __('page.programs.title').' — '.__('brand.name'))

@section('content')
    <section class="bg-bader-green-deep text-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
            <x-bader.section-heading :kicker="__('page.programs.kicker')" :title="__('page.programs.title')" theme="dark" />
            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base">{{ __('page.programs.intro') }}</p>
        </div>
    </section>

    <section class="bg-bader-paper">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:py-18">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($programs as $program)
                    <article class="rounded-2xl border border-bader-green/10 bg-white p-6" data-reveal>
                        <span class="mb-5 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-bader-lime/60" aria-hidden="true">
                            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-6 w-6">
                        </span>
                        <h2 class="text-xl font-semibold text-bader-green">{{ __('program.'.$program) }}</h2>
                        <p class="mt-3 text-sm leading-relaxed text-bader-ink/70">{{ __('program.'.$program.'_text') }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
