@extends('layouts.public')

@section('title', __('errors.404.title').' — '.__('brand.name'))

@section('content')
    <section class="relative isolate overflow-hidden band-tint">
        <div class="pointer-events-none absolute -end-24 -top-24 -z-10 h-80 w-80 rounded-full bg-gold-300/40 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-28 -start-16 -z-10 h-72 w-72 rounded-full bg-forest-500/10 blur-3xl" aria-hidden="true"></div>

        <div class="container-bader py-16 text-center sm:py-24">
            <p class="animate-fade-up text-[7rem] font-extrabold leading-none text-forest-700/15 sm:text-[10rem]" dir="ltr" aria-hidden="true">404</p>
            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="mx-auto -mt-10 h-14 w-14 animate-float sm:-mt-14">
            <h1 class="mt-6 animate-fade-up text-3xl font-extrabold text-ink-900 [animation-delay:80ms] sm:text-4xl">{{ __('errors.404.title') }}</h1>
            <p class="mx-auto mt-4 max-w-xl animate-fade-up leading-8 text-muted [animation-delay:140ms]">{{ __('errors.404.text') }}</p>
            <div class="mt-8 flex animate-fade-up flex-wrap justify-center gap-3 [animation-delay:200ms]">
                <a href="{{ route('home') }}" class="btn-brand min-h-12 px-6">
                    <x-bader.nav-icon route="home" class="h-4 w-4" />
                    {{ __('errors.404.home') }}
                </a>
                <a href="{{ route('donate') }}" class="btn-primary min-h-12 px-6">
                    <x-bader.icon name="heart" class="h-4 w-4" />
                    {{ __('nav.donate') }}
                </a>
            </div>

            <div class="mx-auto mt-14 max-w-3xl">
                <p class="text-sm font-extrabold text-ink-900">{{ __('errors.404.links_title') }}</p>
                <ul class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach (['programs', 'campaigns', 'sponsorship', 'contact'] as $route)
                        <li>
                            <a href="{{ route($route) }}" class="surface-card surface-card-hover flex items-center gap-3 p-4 text-sm font-bold text-ink-800 hover:text-forest-700">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-forest-700">
                                    <x-bader.nav-icon :route="$route" class="h-4 w-4" />
                                </span>
                                {{ __('nav.'.$route) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
@endsection
