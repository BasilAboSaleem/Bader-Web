@extends('layouts.public')

@section('title', __('errors.500.title'))

@section('content')
    <section class="mx-auto max-w-lg px-4 py-24 text-center">
        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" width="72" height="72" class="mx-auto h-16 w-16 opacity-60">
        <p class="mt-6 text-6xl font-bold text-bader-green/30">500</p>
        <h1 class="mt-3 text-2xl font-semibold">{{ __('errors.500.title') }}</h1>
        <p class="mt-3 text-sm leading-relaxed text-bader-ink/70">{{ __('errors.500.text') }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <x-bader.button :href="route('home')">{{ __('errors.404.home') }}</x-bader.button>
            <x-bader.button :href="route('contact')" variant="line">{{ __('nav.contact') }}</x-bader.button>
        </div>
    </section>
@endsection
