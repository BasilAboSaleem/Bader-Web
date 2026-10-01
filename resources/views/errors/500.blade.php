@extends('layouts.public')

@section('title', __('errors.500.title').' — '.__('brand.name'))

@section('content')
    <section class="relative isolate overflow-hidden band-tint">
        <div class="pointer-events-none absolute -end-24 -top-24 -z-10 h-80 w-80 rounded-full bg-gold-300/40 blur-3xl" aria-hidden="true"></div>

        <div class="container-bader py-16 text-center sm:py-24">
            <p class="text-[7rem] font-extrabold leading-none text-forest-700/15 sm:text-[10rem]" dir="ltr" aria-hidden="true">500</p>
            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="mx-auto -mt-10 h-14 w-14 opacity-70 sm:-mt-14">
            <h1 class="mt-6 text-3xl font-extrabold text-ink-900 sm:text-4xl">{{ __('errors.500.title') }}</h1>
            <p class="mx-auto mt-4 max-w-xl leading-8 text-muted">{{ __('errors.500.text') }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="btn-brand min-h-12 px-6">{{ __('errors.404.home') }}</a>
                <a href="{{ route('contact') }}" class="btn-outline min-h-12 px-6">{{ __('nav.contact') }}</a>
            </div>
        </div>
    </section>
@endsection
