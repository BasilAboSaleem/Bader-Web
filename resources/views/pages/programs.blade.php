@extends('layouts.public')

@section('title', __('page.programs.title').' — '.__('brand.name'))

@section('content')
    <x-bader.page-hero
        :kicker="__('page.programs.kicker')"
        :title="__('page.programs.title')"
        :intro="__('page.programs.intro')"
    />

    <section class="bg-sand-50 section-pad">
        <div class="mx-auto max-w-7xl">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($programs as $program)
                    @php
                        $isModel = $program instanceof \App\Models\Program;
                        $title = $isModel ? $program->title : __('program.'.$program);
                        $text = $isModel ? $program->description : __('program.'.$program.'_text');
                    @endphp
                    <article class="card p-6 sm:p-7 hover:border-gold-500/40 transition-all group flex flex-col justify-between" data-reveal>
                        <div>
                            <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-900/10 border border-teal-800/20 group-hover:scale-110 transition-transform">
                                <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-7 w-7 object-contain opacity-90">
                            </div>
                            <h2 class="font-display text-xl font-bold text-teal-950 group-hover:text-forest-700 transition-colors">{{ $title }}</h2>
                            <p class="mt-3 text-sm leading-relaxed text-ink-700/80">{{ $text }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-t border-sand-200 bg-teal-950 section-pad text-center text-sand-50">
        <div class="mx-auto max-w-2xl" data-reveal>
            <h2 class="font-display text-2xl font-bold sm:text-3xl">{{ __('home.programs_explore_more_desc') }}</h2>
            <p class="mt-3 text-sm leading-relaxed text-sand-100/75">{{ __('home.programs_overview_text') }}</p>
            <a href="{{ route('donate') }}" class="btn-primary mt-7">{{ __('nav.donate') }}</a>
        </div>
    </section>
@endsection
