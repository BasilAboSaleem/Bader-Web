@extends('layouts.public')

@section('title', __('page.news.title').' — '.__('brand.name'))
@section('meta_description', __('page.news.intro'))

@php
    $leadStory = $stories->onFirstPage() ? $stories->first() : null;
    $gridStories = $leadStory ? $stories->getCollection()->slice(1) : $stories->getCollection();
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('page.news.kicker')"
        :title="__('page.news.title')"
        :intro="__('page.news.intro')"
    />

    <section class="band-base section-y">
        <div class="container-bader">
            @if ($stories->isEmpty())
                <div class="rounded-3xl border border-dashed border-hairline-strong bg-white p-10 text-center">
                    <x-bader.nav-icon route="news" class="mx-auto h-10 w-10 text-forest-600" />
                    <p class="mt-4 text-muted">{{ __('page.news.empty') }}</p>
                </div>
            @else
                @if ($leadStory)
                    <article class="surface-card surface-card-hover group relative grid grid-cols-1 overflow-hidden lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]" data-reveal>
                        <div class="relative aspect-[16/10] overflow-hidden bg-paper-3 lg:aspect-auto lg:min-h-[22rem]">
                            <img src="{{ $leadStory->image ? asset($leadStory->image) : asset('images/programs/education.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover transition duration-700 ease-bader group-hover:scale-105">
                        </div>
                        <div class="flex flex-col justify-center p-6 sm:p-10">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                                <span class="rounded-full bg-gold-500 px-3 py-1 font-extrabold text-teal-950">{{ __('news_page.latest') }}</span>
                                @if ($leadStory->category)
                                    <span class="rounded-full bg-forest-600/10 px-3 py-1 text-forest-700">{{ $leadStory->category }}</span>
                                @endif
                                @if ($leadStory->published_at)
                                    <time datetime="{{ $leadStory->published_at->toDateString() }}" class="inline-flex items-center gap-1.5 text-subtle">
                                        <x-bader.icon name="calendar" class="h-3.5 w-3.5" />
                                        {{ $leadStory->published_at->translatedFormat('j F Y') }}
                                    </time>
                                @endif
                            </div>
                            <h2 class="mt-4 text-2xl font-extrabold leading-snug text-ink-900 transition group-hover:text-forest-700 sm:text-3xl">
                                <a href="{{ route('news.show', $leadStory->key) }}" class="after:absolute after:inset-0">{{ $leadStory->title }}</a>
                            </h2>
                            @if ($leadStory->excerpt)
                                <p class="mt-3 line-clamp-3 leading-8 text-muted">{{ $leadStory->excerpt }}</p>
                            @endif
                            <span class="mt-6 inline-flex items-center gap-1.5 font-extrabold text-forest-700">
                                {{ __('common.read_more') }}
                                <x-bader.icon name="arrow" class="h-4 w-4 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                            </span>
                        </div>
                    </article>
                @endif

                @if ($gridStories->isNotEmpty())
                    <div @class(['grid gap-6 sm:grid-cols-2 lg:grid-cols-3', 'mt-8' => $leadStory])>
                        @foreach ($gridStories as $story)
                            <x-bader.story-card :story="$story" data-reveal style="animation-delay: {{ ($loop->index % 3) * 80 }}ms" />
                        @endforeach
                    </div>
                @endif

                @if ($stories->hasPages())
                    <div class="mt-10">{{ $stories->links() }}</div>
                @endif
            @endif
        </div>
    </section>

    <x-bader.cta-band :title="__('home.trust_updates_title')" :text="__('home.trust_updates_text')" />
@endsection
