@extends('layouts.public')

@section('title', __('page.'.$key.'.title').' — '.__('brand.name'))

@section('content')
    <section class="bg-bader-green-deep text-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
            <x-bader.section-heading
                :kicker="__('page.'.$key.'.kicker')"
                :title="__('page.'.$key.'.title')"
                theme="dark"
            />
            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base">{{ __('page.'.$key.'.intro') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-14 sm:py-18">
        <div class="grid gap-5 md:grid-cols-3">
            @foreach ($sections as $section)
                <article class="rounded-2xl border border-bader-green/10 bg-white p-6 shadow-[0_1px_0_rgba(16,36,24,0.04)]" data-reveal>
                    <span class="mb-5 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-bader-lime/60 text-bader-green" aria-hidden="true">
                        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-6 w-6">
                    </span>
                    <h2 class="text-xl font-semibold text-bader-green">{{ __('page.'.$key.'.'.$section.'.title') }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-bader-ink/70">{{ __('page.'.$key.'.'.$section.'.text') }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="border-y border-bader-green/10 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap gap-3 px-4 py-10">
            <x-bader.button :href="route('donate')">{{ __('nav.donate') }}</x-bader.button>
            <x-bader.button :href="route('contact')" variant="line">{{ __('nav.contact') }}</x-bader.button>
        </div>
    </section>
@endsection
