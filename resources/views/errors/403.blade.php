@extends('layouts.public')

@section('title', __('errors.403.title').' — '.__('brand.name'))

@section('content')
    <section class="relative isolate overflow-hidden band-tint">
        <div class="pointer-events-none absolute -end-24 -top-24 -z-10 h-80 w-80 rounded-full bg-gold-300/40 blur-3xl" aria-hidden="true"></div>

        <div class="container-bader py-16 text-center sm:py-24">
            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-white shadow-card-md ring-1 ring-hairline">
                <x-bader.icon name="lock" class="h-9 w-9 text-forest-700" />
            </span>
            <h1 class="mt-8 text-3xl font-extrabold text-ink-900 sm:text-4xl">{{ __('errors.403.title') }}</h1>
            <p class="mx-auto mt-4 max-w-xl leading-8 text-muted">{{ __('errors.403.text') }}</p>
            <div class="mt-8">
                <a href="{{ route('home') }}" class="btn-brand min-h-12 px-6">{{ __('errors.404.home') }}</a>
            </div>
        </div>
    </section>
@endsection
