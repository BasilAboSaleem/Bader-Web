@extends('layouts.dashboard')

@section('title', __('dashboard.title'))
@section('heading', __('dashboard.overview'))

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-linear-to-l from-bader-green-deep to-bader-green p-6 text-white shadow-sm sm:p-8">
        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-bader-lime">
                <span class="h-2 w-2 rounded-full bg-bader-lime"></span>
                <span>{{ __('dashboard.status_active') }} · {{ __('brand.name') }}</span>
            </div>
            <h2 class="mt-3 text-2xl font-bold tracking-tight sm:text-3xl">{{ __('dashboard.welcome_title') }}</h2>
            <p class="mt-2 text-sm text-white/80 leading-relaxed sm:text-base">
                {{ __('dashboard.welcome_desc') }}
            </p>
        </div>
        <div class="absolute end-6 bottom-[-20px] hidden opacity-10 sm:block pointer-events-none">
            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" width="200" height="200" class="h-48 w-48">
        </div>
    </div>

    <!-- Quick Status Grid -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($quickStats as $stat)
            <div class="rounded-2xl border border-bader-green/10 bg-white p-5 shadow-xs transition hover:border-bader-green/30">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-bader-ink/60">{{ __($stat['label']) }}</p>
                    <span class="inline-flex items-center rounded-full bg-bader-paper px-2.5 py-0.5 text-xs font-semibold text-bader-green">
                        {{ __($stat['badge']) }}
                    </span>
                </div>
                <p class="mt-3 text-lg font-bold text-bader-ink">{{ __($stat['value']) }}</p>
            </div>
        @endforeach
    </div>

    <!-- Modules Architecture Grid -->
    <div>
        <div class="mb-4">
            <h3 class="text-lg font-bold text-bader-ink">{{ __('dashboard.modules_title') }}</h3>
            <p class="text-xs text-bader-ink/65">{{ __('dashboard.modules_subtitle') }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($modules as $module)
                <div class="flex flex-col justify-between rounded-2xl border border-bader-green/10 bg-white p-5 shadow-xs transition hover:border-bader-green/30">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="font-bold text-bader-ink">{{ __('dashboard.module.'.$module['key']) }}</h4>
                            <span class="inline-flex items-center rounded-md bg-bader-paper px-2 py-0.5 text-[11px] font-semibold text-bader-green-deep">
                                {{ __('dashboard.status_soon') }} (المرحلة {{ $module['stage'] }})
                            </span>
                        </div>
                        <p class="mt-2 text-xs leading-relaxed text-bader-ink/70">
                            {{ __('dashboard.module.'.$module['key'].'_desc') }}
                        </p>
                    </div>

                    <div class="mt-4 border-t border-bader-green/5 pt-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-bader-ink/50 cursor-not-allowed">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>قيد التفعيل في المرحلة {{ $module['stage'] }}</span>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
