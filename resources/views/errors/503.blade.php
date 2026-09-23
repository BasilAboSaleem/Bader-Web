@extends('layouts.public')

@section('title', __('errors.503.title'))

@section('content')
    <section class="mx-auto max-w-lg px-4 py-24 text-center">
        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" width="72" height="72" class="mx-auto h-16 w-16">
        <p class="mt-6 text-6xl font-bold text-bader-green/20">503</p>
        <h1 class="mt-3 text-2xl font-semibold">{{ __('errors.503.title') }}</h1>
        <p class="mt-3 text-sm leading-relaxed text-bader-ink/70">{{ __('errors.503.text') }}</p>
        <p class="mt-6 text-xs text-bader-ink/50">{{ __('errors.503.note') }}</p>
        <div class="mt-8">
            <x-bader.button :href="route('home')">{{ __('errors.503.retry') }}</x-bader.button>
        </div>
    </section>
@endsection
