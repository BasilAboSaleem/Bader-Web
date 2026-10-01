@extends('layouts.public')

@section('title', __('page.news.title').' — '.__('brand.name'))

@section('content')
    {{-- Hero Banner --}}
    <section class="bg-teal-950 text-sand-50 section-pad !py-20 relative overflow-hidden">
        <div class="pointer-events-none absolute -end-24 top-0 h-96 w-96 rounded-full bg-forest-700/20 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute start-0 bottom-0 h-80 w-80 rounded-full bg-gold-500/10 blur-3xl" aria-hidden="true"></div>

        <div class="max-w-6xl mx-auto relative z-10 text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-teal-900/90 border border-teal-700/60 px-4 py-1.5 text-xs font-mono text-gold-400 font-semibold mb-4 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-gold-400 animate-pulse"></span>
                <span>{{ __('page.news.kicker') }}</span>
            </div>
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-sand-50 max-w-3xl mx-auto leading-tight">
                {{ __('page.news.title') }}
            </h1>
            <p class="mt-5 text-base sm:text-lg text-sand-100/80 max-w-2xl mx-auto leading-relaxed">
                {{ __('page.news.intro') }}
            </p>
        </div>
    </section>

    {{-- News Grid --}}
    <section class="bg-sand-50 section-pad">
        <div class="max-w-6xl mx-auto">
            @if ($stories->isEmpty())
                <div class="text-center py-20 text-ink-700/50 text-base">
                    {{ __('page.news.empty') }}
                </div>
            @else
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($stories as $story)
                        <article class="group rounded-3xl overflow-hidden bg-white border border-sand-200/90 shadow-md hover:shadow-2xl transition-all duration-500 flex flex-col justify-between" data-reveal>
                            <div>
                                {{-- Photo --}}
                                <a href="{{ route('news.show', $story->key) }}" class="block relative h-48 w-full overflow-hidden bg-teal-950">
                                    @if ($story->image)
                                        <img
                                            src="{{ asset($story->image) }}"
                                            alt="{{ $story->title }}"
                                            class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-108"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="h-full w-full bg-gradient-to-br from-teal-800 to-forest-900 flex items-center justify-center">
                                            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-12 w-12 object-contain opacity-30">
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-teal-950/80 via-transparent to-transparent"></div>

                                    {{-- Category --}}
                                    @if ($story->category)
                                        <div class="absolute top-3.5 start-3.5">
                                            <span class="rounded-full bg-teal-950/85 backdrop-blur-md px-3 py-1 text-xs font-bold text-gold-400 border border-teal-700/60 shadow">
                                                {{ $story->category }}
                                            </span>
                                        </div>
                                    @endif

                                    @if ($story->is_featured)
                                        <div class="absolute top-3.5 end-3.5">
                                            <span class="rounded-full bg-gold-500/90 backdrop-blur-md px-3 py-1 text-xs font-bold text-teal-950 shadow">
                                                {{ app()->isLocale('ar') ? 'مميز' : 'Featured' }}
                                            </span>
                                        </div>
                                    @endif
                                </a>

                                {{-- Body --}}
                                <div class="p-6">
                                    <div class="flex items-center gap-2 text-xs text-ink-700/60 font-mono mb-3">
                                        <svg class="h-3.5 w-3.5 text-forest-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <time datetime="{{ $story->published_at?->toDateString() }}">
                                            {{ $story->published_at?->format('Y-m-d') }}
                                        </time>
                                    </div>
                                    <h2 class="font-display text-lg font-bold text-ink-900 group-hover:text-forest-700 transition-colors leading-snug">
                                        <a href="{{ route('news.show', $story->key) }}">
                                            {{ $story->title }}
                                        </a>
                                    </h2>
                                    @if ($story->excerpt)
                                        <p class="mt-2.5 text-sm text-ink-700/75 leading-relaxed line-clamp-3">{{ $story->excerpt }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="p-6 pt-0 border-t border-sand-100 flex items-center justify-between text-xs font-bold text-forest-700 group-hover:text-forest-600">
                                <a href="{{ route('news.show', $story->key) }}" class="inline-flex items-center gap-1">
                                    <span>{{ app()->isLocale('ar') ? 'اقرأ الخبر كاملاً' : 'Read full story' }}</span>
                                    <span aria-hidden="true" class="transition-transform group-hover:-translate-x-1 font-mono text-sm rtl:rotate-180">&rarr;</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($stories->hasPages())
                    <div class="mt-12 flex justify-center">
                        {{ $stories->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
