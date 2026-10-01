@extends('layouts.public')

@section('title', $story->title . ' — ' . __('brand.name'))

@section('content')
    {{-- Article Container --}}
    <div class="bg-sand-50/60 min-h-screen py-8 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. Breadcrumb & Top Navigation --}}
            <div class="flex items-center justify-between gap-4 mb-6 text-xs sm:text-sm font-mono">
                <nav class="flex items-center gap-2 text-ink-700/70" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-forest-700 transition-colors flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <span>{{ __('nav.home') }}</span>
                    </a>
                    <span class="text-sand-300">/</span>
                    <a href="{{ route('news') }}" class="hover:text-forest-700 transition-colors">{{ __('nav.news') }}</a>
                    <span class="text-sand-300">/</span>
                    <span class="text-forest-700 font-bold truncate max-w-[180px] sm:max-w-xs">{{ $story->title }}</span>
                </nav>

                <a href="{{ route('news') }}" class="inline-flex items-center gap-1.5 rounded-full bg-white px-3.5 py-1.5 text-xs font-bold text-forest-700 border border-sand-200 shadow-sm hover:bg-forest-700 hover:text-white hover:border-forest-700 transition-all">
                    <span aria-hidden="true" class="rtl:rotate-180 font-mono">&larr;</span>
                    <span>{{ app()->isLocale('ar') ? 'جميع الأخبار' : 'All News' }}</span>
                </a>
            </div>

            {{-- 2. Article Header Card --}}
            <header class="bg-white rounded-3xl p-6 sm:p-10 border border-sand-200/90 shadow-sm mb-8">
                {{-- Tags & Time Metadata --}}
                <div class="flex flex-wrap items-center gap-2.5 sm:gap-3 mb-5">
                    @if ($story->category)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-forest-700/10 border border-forest-700/20 px-3.5 py-1 text-xs font-bold text-forest-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-forest-600"></span>
                            <span>{{ $story->category }}</span>
                        </span>
                    @endif

                    @if ($story->is_featured)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-500/20 border border-gold-500/50 text-teal-950 px-3 py-1 text-xs font-bold">
                            <svg class="h-3 w-3 fill-gold-600" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span>{{ app()->isLocale('ar') ? 'تقرير مميز' : 'Featured' }}</span>
                        </span>
                    @endif

                    @if ($story->published_at)
                        <div class="inline-flex items-center gap-1.5 text-xs text-ink-700/70 font-mono bg-sand-50 rounded-full px-3 py-1 border border-sand-200/60">
                            <svg class="h-3.5 w-3.5 text-forest-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <time datetime="{{ $story->published_at->toDateString() }}">
                                {{ $story->published_at->format('Y-m-d') }}
                            </time>
                        </div>
                    @endif

                    <span class="inline-flex items-center gap-1 text-xs text-ink-700/60 font-mono">
                        <svg class="h-3.5 w-3.5 text-ink-700/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ app()->isLocale('ar') ? 'دقيقتان قراءة' : '2 min read' }}</span>
                    </span>
                </div>

                {{-- Title --}}
                <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-ink-900 leading-tight sm:leading-snug tracking-tight mb-5">
                    {{ $story->title }}
                </h1>

                {{-- Excerpt Highlight --}}
                @if ($story->excerpt)
                    <div class="rounded-2xl bg-sand-50 border-s-4 border-gold-500 p-4 sm:p-5 text-ink-800 text-sm sm:text-base leading-relaxed font-sans shadow-xs mb-6">
                        <p>{{ $story->excerpt }}</p>
                    </div>
                @endif

                {{-- Interactive Reader Utility Bar (Font scale, Share, Copy) --}}
                <div class="flex flex-wrap items-center justify-between gap-4 pt-5 border-t border-sand-100 text-xs text-ink-700/70 font-mono">
                    {{-- Author / Source --}}
                    <div class="flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-full bg-teal-950 text-gold-400 flex items-center justify-center font-bold shadow-xs shrink-0">
                            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-4 w-4 object-contain">
                        </div>
                        <div>
                            <span class="font-bold text-ink-900 block font-sans">{{ __('brand.name') }}</span>
                            <span class="text-[11px] text-ink-700/60">{{ app()->isLocale('ar') ? 'المكتب الإعلامي الميداني' : 'Field Media Office' }}</span>
                        </div>
                    </div>

                    {{-- Reading Utilities & Social Actions --}}
                    <div class="flex items-center gap-3">
                        {{-- Font Size Adjuster --}}
                        <div class="flex items-center bg-sand-100/80 rounded-xl p-0.5 border border-sand-200">
                            <button
                                type="button"
                                onclick="adjustArticleFontSize(-1)"
                                title="{{ app()->isLocale('ar') ? 'تصغير الخط' : 'Smaller text' }}"
                                class="h-7 w-7 rounded-lg flex items-center justify-center hover:bg-white text-ink-700 hover:text-ink-900 font-bold transition-all text-xs"
                            >A-</button>
                            <button
                                type="button"
                                onclick="adjustArticleFontSize(0)"
                                title="{{ app()->isLocale('ar') ? 'الحجم الافتراضي' : 'Default text size' }}"
                                class="h-7 px-1.5 rounded-lg flex items-center justify-center hover:bg-white text-ink-700 hover:text-ink-900 font-bold transition-all text-xs"
                            >A</button>
                            <button
                                type="button"
                                onclick="adjustArticleFontSize(1)"
                                title="{{ app()->isLocale('ar') ? 'تكبير الخط' : 'Larger text' }}"
                                class="h-7 w-7 rounded-lg flex items-center justify-center hover:bg-white text-ink-700 hover:text-ink-900 font-bold transition-all text-xs"
                            >A+</button>
                        </div>

                        <div class="h-4 w-px bg-sand-200"></div>

                        {{-- Social Share Icons --}}
                        <div class="flex items-center gap-1.5">
                            {{-- Copy Link Button --}}
                            <button
                                type="button"
                                onclick="navigator.clipboard.writeText(window.location.href); const el = this.querySelector('.copy-notify'); el.classList.remove('hidden'); setTimeout(() => el.classList.add('hidden'), 2000);"
                                title="{{ app()->isLocale('ar') ? 'نسخ الرابط' : 'Copy link' }}"
                                class="relative inline-flex h-8 w-8 items-center justify-center rounded-xl bg-sand-50 border border-sand-200 text-ink-700 hover:bg-forest-700 hover:text-white hover:border-forest-700 transition-all shadow-xs"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span class="copy-notify hidden absolute -top-8 start-1/2 -translate-x-1/2 rounded-md bg-ink-900 px-2 py-0.5 text-[10px] text-white whitespace-nowrap shadow-md z-30 font-sans">
                                    {{ app()->isLocale('ar') ? 'تم النسخ!' : 'Copied!' }}
                                </span>
                            </button>

                            {{-- WhatsApp Share --}}
                            <a
                                href="https://api.whatsapp.com/send?text={{ urlencode($story->title . "\n" . url()->current()) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="WhatsApp"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-xs"
                            >
                                <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.861.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.203c.043.071.043.419-.101.824z"/>
                                </svg>
                            </a>

                            {{-- X (Twitter) --}}
                            <a
                                href="https://twitter.com/intent/tweet?text={{ urlencode($story->title) }}&url={{ urlencode(url()->current()) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="X / Twitter"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-sand-50 border border-sand-200 text-ink-900 hover:bg-black hover:text-white hover:border-black transition-all shadow-xs"
                            >
                                <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            {{-- 3. Featured Cover Image --}}
            @if ($story->image)
                <div class="relative w-full rounded-3xl overflow-hidden border border-sand-200 bg-teal-950 shadow-md mb-8 group">
                    <img
                        id="article-cover-image"
                        src="{{ asset($story->image) }}"
                        alt="{{ $story->title }}"
                        class="w-full h-auto max-h-[520px] object-cover cursor-zoom-in transition-transform duration-700 group-hover:scale-102"
                        loading="eager"
                        onclick="openImageModal(this.src)"
                    >
                    <div class="absolute bottom-3 start-4 end-4 flex items-center justify-between text-xs text-white/90 font-mono pointer-events-none">
                        <span class="inline-flex items-center gap-1.5 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full border border-white/20">
                            <span class="h-1.5 w-1.5 rounded-full bg-gold-400"></span>
                            <span>{{ app()->isLocale('ar') ? 'توثيق ميداني — قطاع غزة' : 'Field Photo — Gaza' }}</span>
                        </span>
                        <span class="hidden sm:inline-flex items-center gap-1 bg-black/40 backdrop-blur-md px-2.5 py-1 rounded-full text-[11px]">
                            <svg class="h-3 w-3 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" /></svg>
                            <span>{{ app()->isLocale('ar') ? 'تكبير الصورة' : 'Zoom' }}</span>
                        </span>
                    </div>
                </div>
            @endif

            {{-- 4. Full Article Body Card --}}
            <article class="bg-white rounded-3xl p-6 sm:p-10 lg:p-12 border border-sand-200 shadow-sm mb-8">
                {{-- Body Text Container with Dynamic Text Sizing --}}
                <div id="article-body-text" class="text-ink-900 font-sans text-base sm:text-lg leading-loose space-y-6 transition-all duration-200">
                    @if ($story->content)
                        @foreach (preg_split("/\r\n|\n|\r/", $story->content) as $paragraph)
                            @if (trim($paragraph))
                                <p class="text-ink-800/95 leading-relaxed">{{ trim($paragraph) }}</p>
                            @endif
                        @endforeach
                    @else
                        <p class="text-ink-800 leading-relaxed">{{ $story->excerpt }}</p>
                    @endif
                </div>

                {{-- Official Field Verification Box --}}
                <div class="mt-10 rounded-2xl bg-forest-700/5 border border-forest-700/15 p-5 sm:p-6 flex items-center gap-4">
                    <div class="h-11 w-11 rounded-2xl bg-forest-700 text-gold-400 flex items-center justify-center shrink-0 shadow-xs">
                        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-6 w-6 object-contain">
                    </div>
                    <div class="text-xs sm:text-sm text-ink-800 leading-relaxed font-sans">
                        <span class="font-bold text-forest-800 block mb-0.5">{{ app()->isLocale('ar') ? 'بيان ميداني موثق — مؤسسة بادر' : 'Official Field Release — Bader' }}</span>
                        <span class="text-ink-700/80">{{ app()->isLocale('ar') ? 'تم إعداد هذا المحتوى ونشره عبر فرقنا الميدانية العاملة في قطاع غزة.' : 'Prepared and released directly by Bader field operators in Gaza.' }}</span>
                    </div>
                </div>

                {{-- Support & Donate Callout --}}
                <div class="mt-8 rounded-2xl bg-teal-950 bg-bader-green-deep text-sand-50 p-6 sm:p-7 border border-teal-800 relative overflow-hidden flex flex-col sm:flex-row items-center justify-between gap-5 shadow-lg">
                    <div class="space-y-1 text-center sm:text-start">
                        <h3 class="font-display text-lg sm:text-xl font-bold text-white">
                            {{ app()->isLocale('ar') ? 'كن شريكاً في صناعة هذا الأثر' : 'Partner with us in creating impact' }}
                        </h3>
                        <p class="text-xs sm:text-sm text-sand-100/80 font-sans">
                            {{ app()->isLocale('ar') ? 'مساهمتك تساعد في استمرار وتوسيع هذه المشاريع الإنسانية.' : 'Your support helps sustain and expand these humanitarian operations.' }}
                        </p>
                    </div>
                    <a href="{{ route('donate') }}" class="btn-primary !px-7 !py-3 !text-sm font-bold shrink-0 shadow-lg shadow-gold-500/20 flex items-center gap-2">
                        <span>{{ __('nav.donate') }}</span>
                        <span aria-hidden="true" class="rtl:rotate-180">&rarr;</span>
                    </a>
                </div>
            </article>

            {{-- 5. Next & Previous Story Navigation Cards --}}
            @if ($prevStory || $nextStory)
                <div class="grid gap-4 sm:grid-cols-2 mb-10">
                    @if ($prevStory)
                        <a href="{{ route('news.show', $prevStory->key) }}" class="group bg-white rounded-2xl p-5 border border-sand-200 hover:border-forest-700/50 hover:shadow-md transition-all flex flex-col justify-between">
                            <span class="text-[11px] font-mono font-semibold text-forest-700 mb-2 flex items-center gap-1">
                                <span aria-hidden="true" class="rtl:rotate-180">&larr;</span>
                                <span>{{ app()->isLocale('ar') ? 'التقرير السابق' : 'Previous Report' }}</span>
                            </span>
                            <h4 class="font-display text-sm font-bold text-ink-900 group-hover:text-forest-700 transition-colors line-clamp-2 leading-snug">
                                {{ $prevStory->title }}
                            </h4>
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if ($nextStory)
                        <a href="{{ route('news.show', $nextStory->key) }}" class="group bg-white rounded-2xl p-5 border border-sand-200 hover:border-forest-700/50 hover:shadow-md transition-all flex flex-col justify-between text-end sm:text-start">
                            <span class="text-[11px] font-mono font-semibold text-forest-700 mb-2 flex items-center gap-1 justify-end sm:justify-start">
                                <span>{{ app()->isLocale('ar') ? 'التقرير التالي' : 'Next Report' }}</span>
                                <span aria-hidden="true" class="rtl:rotate-180">&rarr;</span>
                            </span>
                            <h4 class="font-display text-sm font-bold text-ink-900 group-hover:text-forest-700 transition-colors line-clamp-2 leading-snug">
                                {{ $nextStory->title }}
                            </h4>
                        </a>
                    @endif
                </div>
            @endif

            {{-- 6. Related Stories Grid --}}
            @if ($relatedStories->isNotEmpty())
                <section class="mt-12 pt-10 border-t border-sand-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-display text-xl sm:text-2xl font-bold text-ink-900">
                            {{ app()->isLocale('ar') ? 'أخبار وتقارير ميدانية أخرى' : 'More Field Reports' }}
                        </h3>
                        <a href="{{ route('news') }}" class="text-xs sm:text-sm font-bold text-forest-700 hover:text-forest-800 inline-flex items-center gap-1">
                            <span>{{ app()->isLocale('ar') ? 'عرض الكل' : 'View All' }}</span>
                            <span aria-hidden="true" class="rtl:rotate-180">&rarr;</span>
                        </a>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        @foreach ($relatedStories as $related)
                            <article class="group rounded-2xl overflow-hidden bg-white border border-sand-200 hover:shadow-lg transition-all duration-300 flex flex-col justify-between">
                                <div>
                                    <a href="{{ route('news.show', $related->key) }}" class="block relative h-40 w-full overflow-hidden bg-teal-950">
                                        @if ($related->image)
                                            <img
                                                src="{{ asset($related->image) }}"
                                                alt="{{ $related->title }}"
                                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="h-full w-full bg-gradient-to-br from-teal-800 to-forest-900 flex items-center justify-center">
                                                <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-10 w-10 object-contain opacity-25">
                                            </div>
                                        @endif

                                        @if ($related->category)
                                            <div class="absolute top-2.5 start-2.5">
                                                <span class="rounded-full bg-teal-950/85 px-2.5 py-0.5 text-[10px] font-bold text-gold-400 border border-teal-700/60 shadow">
                                                    {{ $related->category }}
                                                </span>
                                            </div>
                                        @endif
                                    </a>

                                    <div class="p-4">
                                        @if ($related->published_at)
                                            <div class="text-[11px] text-ink-700/60 font-mono mb-1.5">
                                                {{ $related->published_at->format('Y-m-d') }}
                                            </div>
                                        @endif
                                        <h4 class="font-display text-sm font-bold text-ink-900 group-hover:text-forest-700 transition-colors leading-snug line-clamp-2">
                                            <a href="{{ route('news.show', $related->key) }}">
                                                {{ $related->title }}
                                            </a>
                                        </h4>
                                    </div>
                                </div>

                                <div class="p-4 pt-0 border-t border-sand-100 flex items-center justify-between text-xs font-bold text-forest-700">
                                    <a href="{{ route('news.show', $related->key) }}" class="inline-flex items-center gap-1">
                                        <span>{{ app()->isLocale('ar') ? 'اقرأ التقرير' : 'Read' }}</span>
                                        <span aria-hidden="true" class="rtl:rotate-180">&rarr;</span>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>
    </div>

    {{-- Lightbox Zoom Modal for Photo --}}
    <div id="image-lightbox-modal" class="fixed inset-0 z-[100] bg-black/90 backdrop-blur-md hidden items-center justify-center p-4 transition-opacity duration-300" onclick="closeImageModal()">
        <button type="button" class="absolute top-6 end-6 text-white hover:text-gold-400 p-2 focus:outline-none" onclick="closeImageModal()">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        <img id="lightbox-modal-img" src="" alt="Full view" class="max-w-full max-h-[90vh] object-contain rounded-2xl shadow-2xl border border-white/20">
    </div>

    {{-- Font Scaler & Lightbox Scripts --}}
    <script>
        let currentFontSize = 0; // -1: small, 0: default, 1: large
        const sizeClasses = ['!text-sm !leading-relaxed', '!text-base sm:!text-lg !leading-loose', '!text-lg sm:!text-xl !leading-loose'];

        function adjustArticleFontSize(step) {
            const body = document.getElementById('article-body-text');
            if (!body) return;

            if (step === 0) {
                currentFontSize = 0;
            } else {
                currentFontSize = Math.max(-1, Math.min(1, currentFontSize + step));
            }

            body.classList.remove('!text-sm', '!text-base', '!text-lg', '!text-xl', '!leading-relaxed', '!leading-loose', 'sm:!text-lg', 'sm:!text-xl');

            if (currentFontSize === -1) {
                body.classList.add('!text-sm', '!leading-relaxed');
            } else if (currentFontSize === 1) {
                body.classList.add('!text-lg', 'sm:!text-xl', '!leading-loose');
            } else {
                body.classList.add('!text-base', 'sm:!text-lg', '!leading-loose');
            }
        }

        function openImageModal(src) {
            const modal = document.getElementById('image-lightbox-modal');
            const img = document.getElementById('lightbox-modal-img');
            if (modal && img && src) {
                img.src = src;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeImageModal() {
            const modal = document.getElementById('image-lightbox-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
@endsection
