@extends('layouts.public')

@section('title', __('errors.503.title').' — '.__('brand.name'))

@section('content')
    <section class="relative isolate overflow-hidden band-tint">
        <div class="pointer-events-none absolute -end-24 -top-24 -z-10 h-80 w-80 rounded-full bg-gold-300/40 blur-3xl" aria-hidden="true"></div>

        <div class="container-bader py-16 text-center sm:py-24">
            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-white shadow-card-md ring-1 ring-hairline">
                <x-bader.icon name="clock" class="h-9 w-9 text-forest-700" />
            </span>
            <h1 class="mt-8 text-3xl font-extrabold text-ink-900 sm:text-4xl">{{ __('errors.503.title') }}</h1>
            <p class="mx-auto mt-4 max-w-xl leading-8 text-muted">{{ __('errors.503.text') }}</p>
            <p class="mx-auto mt-3 max-w-xl text-sm text-subtle">{{ __('errors.503.note') }}</p>
            <div class="mt-8">
                <a href="{{ url()->current() }}" class="btn-brand min-h-12 px-6">
                    <x-bader.icon name="repeat" class="h-4 w-4" />
                    {{ __('errors.503.retry') }}
                </a>
            </div>
        </div>
    </section>
@endsection
