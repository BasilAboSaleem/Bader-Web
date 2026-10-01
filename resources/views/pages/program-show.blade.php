@extends('layouts.public')

@php
    $fallbackImage = 'images/programs/'.$program->key.'.jpg';
    $programImage = $program->image ?: (file_exists(public_path($fallbackImage)) ? $fallbackImage : 'images/programs/water.jpg');
@endphp

@section('title', $program->title.' — '.__('brand.name'))
@section('meta_description', \Illuminate\Support\Str::limit((string) $program->description, 160))

@section('content')
    <x-bader.page-hero
        :kicker="$program->category ?: __('page.programs.kicker')"
        :title="$program->title"
        :breadcrumbs="[['label' => __('nav.programs'), 'url' => route('programs')]]"
    />

    <section class="band-base section-y">
        <div class="container-bader grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="min-w-0">
                <div class="overflow-hidden rounded-3xl bg-paper-3 shadow-card-md" data-reveal>
                    <img src="{{ asset($programImage) }}" alt="{{ $program->title }}" class="aspect-[16/9] w-full object-cover">
                </div>

                <div class="mt-8" data-reveal>
                    @if ($program->badge)
                        <span class="inline-flex rounded-full bg-gold-500 px-3 py-1 text-xs font-extrabold text-teal-950">{{ $program->badge }}</span>
                    @endif
                    @if ($program->description)
                        <p class="mt-4 text-lg leading-9 text-ink-800">{{ $program->description }}</p>
                    @endif
                    @if ($program->highlight)
                        <p class="mt-6 flex items-start gap-3 rounded-2xl border border-hairline bg-paper-2 p-5 font-semibold text-ink-800">
                            <x-bader.icon name="sparkle" class="mt-1 h-5 w-5 shrink-0 text-forest-600" />
                            {{ $program->highlight }}
                        </p>
                    @endif
                </div>

                <div class="mt-12">
                    <div class="flex items-end justify-between gap-4" data-reveal>
                        <div>
                            <p class="kicker">{{ __('program_page.projects_kicker') }}</p>
                            <h2 class="section-title mt-2">{{ __('program_page.projects_title', ['program' => $program->title]) }}</h2>
                        </div>
                    </div>

                    @if ($campaigns->isNotEmpty())
                        <div class="mt-6 grid gap-6 sm:grid-cols-2">
                            @foreach ($campaigns as $campaign)
                                <x-bader.project-card :campaign="$campaign" />
                            @endforeach
                        </div>
                    @else
                        <div class="mt-6 rounded-3xl border border-dashed border-hairline-strong bg-white p-8 text-center">
                            <p class="text-muted">{{ __('program_page.no_projects') }}</p>
                            <a href="{{ route('donate', ['target_type' => 'program', 'target_id' => $program->id]) }}" class="btn-brand mt-5">{{ __('program_page.support_program') }}</a>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="lg:sticky lg:top-28" aria-label="{{ __('donate_box.kicker') }}">
                <x-bader.donate-box
                    :title="__('program_page.support_program_title', ['program' => $program->title])"
                    :donate-params="['target_type' => 'program', 'target_id' => $program->id]"
                >
                    <p class="text-sm leading-relaxed text-muted">{{ __('program_page.donate_note') }}</p>
                </x-bader.donate-box>
            </aside>
        </div>
    </section>

    @if ($otherPrograms->isNotEmpty())
        <section class="band-tint section-y">
            <div class="container-bader">
                <h2 class="section-title" data-reveal>{{ __('program_page.other_programs') }}</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($otherPrograms as $otherProgram)
                        <a href="{{ route('programs.show', $otherProgram->key) }}" class="surface-card surface-card-hover group p-5" data-reveal>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-forest-600/10 text-forest-700 group-hover:bg-forest-700 group-hover:text-white">
                                <x-bader.icon name="sprout" class="h-5 w-5" />
                            </span>
                            <h3 class="mt-4 font-extrabold text-ink-900 group-hover:text-forest-700">{{ $otherProgram->title }}</h3>
                            <p class="mt-1 text-xs text-subtle">{{ trans_choice('region.projects_count', $otherProgram->campaigns_count, ['count' => $otherProgram->campaigns_count]) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
