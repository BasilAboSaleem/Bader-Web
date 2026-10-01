@extends('layouts.public')

@section('title', __('page.programs.title').' — '.__('brand.name'))
@section('meta_description', __('page.programs.intro'))

@section('content')
    <x-bader.page-hero
        :kicker="__('page.programs.kicker')"
        :title="__('page.programs.title')"
        :intro="__('page.programs.intro')"
    />

    <section class="band-base section-y">
        <div class="container-bader">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($programs as $program)
                    @php
                        $fallbackImage = 'images/programs/'.$program->key.'.jpg';
                        $programImage = $program->image ?: (file_exists(public_path($fallbackImage)) ? $fallbackImage : 'images/programs/water.jpg');
                    @endphp
                    <article class="surface-card surface-card-hover group relative flex flex-col overflow-hidden" data-reveal style="animation-delay: {{ ($loop->index % 3) * 70 }}ms">
                        <div class="relative aspect-[16/10] overflow-hidden bg-paper-3">
                            <img src="{{ asset($programImage) }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-700 ease-bader group-hover:scale-105">
                            <span class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/40 to-transparent"></span>
                            @if ($program->badge)
                                <span class="absolute start-3 top-3 rounded-full bg-gold-500 px-3 py-1 text-xs font-extrabold text-teal-950">{{ $program->badge }}</span>
                            @endif
                            <span class="absolute bottom-3 start-3 inline-flex items-center gap-1.5 text-xs font-bold text-white">
                                <x-bader.icon name="grid" class="h-3.5 w-3.5" />
                                {{ trans_choice('region.projects_count', $program->campaigns_count, ['count' => $program->campaigns_count]) }}
                            </span>
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            @if ($program->category)
                                <p class="text-xs font-extrabold text-forest-700">{{ $program->category }}</p>
                            @endif
                            <h2 class="mt-1 text-xl font-extrabold text-ink-900 group-hover:text-forest-700">
                                <a href="{{ route('programs.show', $program->key) }}" class="after:absolute after:inset-0">{{ $program->title }}</a>
                            </h2>
                            @if ($program->description)
                                <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-muted">{{ $program->description }}</p>
                            @endif
                            <div class="relative z-10 mt-auto flex gap-2 pt-6">
                                <a href="{{ route('donate', ['target_type' => 'program', 'target_id' => $program->id]) }}" class="btn-primary flex-1">
                                    <x-bader.icon name="heart" class="h-4 w-4" />
                                    {{ __('home.programs_donate_action') }}
                                </a>
                                <a href="{{ route('programs.show', $program->key) }}" class="btn-outline">{{ __('home.programs_details_action') }}</a>
                            </div>
                        </div>
                    </article>
                @endforeach

                @foreach ($fallbackPrograms as $programKey)
                    <article class="surface-card flex flex-col p-6" data-reveal>
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-forest-600/10">
                            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-6 w-6 object-contain">
                        </span>
                        <h2 class="mt-5 text-xl font-extrabold text-ink-900">{{ __('program.'.$programKey) }}</h2>
                        <p class="mt-3 text-sm leading-relaxed text-muted">{{ __('program.'.$programKey.'_text') }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="band-anchor section-y text-center">
        <div class="container-bader max-w-2xl" data-reveal>
            <h2 class="text-2xl font-extrabold sm:text-3xl">{{ __('home.programs_explore_more_desc') }}</h2>
            <p class="mt-3 text-sm leading-relaxed text-white/75">{{ __('home.programs_overview_text') }}</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('donate') }}" class="btn-primary">
                    <x-bader.icon name="heart" class="h-4 w-4" />
                    {{ __('nav.donate') }}
                </a>
                <a href="{{ route('campaigns') }}" class="btn-ghost">{{ __('header.all_projects') }}</a>
            </div>
        </div>
    </section>
@endsection
