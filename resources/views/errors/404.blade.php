@extends('layouts.public')

@section('title', __('errors.404.title'))

@section('content')
    <section class="mx-auto max-w-lg px-4 py-24 text-center">
        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" width="72" height="72" class="mx-auto h-16 w-16">
        <h1 class="mt-6 text-3xl font-semibold">{{ __('errors.404.title') }}</h1>
        <p class="mt-3 text-sm leading-relaxed text-bader-ink/70">{{ __('errors.404.text') }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <x-bader.button :href="route('home')">{{ __('errors.404.home') }}</x-bader.button>
            <x-bader.button :href="route('donate')" variant="line">{{ __('nav.donate') }}</x-bader.button>
        </div>
    </section>
@endsection
