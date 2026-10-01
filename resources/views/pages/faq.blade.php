@extends('layouts.public')

@use('App\Support\SiteSettings')

@section('title', __('page.faq.title').' — '.__('brand.name'))
@section('meta_description', SiteSettings::pageIntro('faq'))

@php
    $whatsappNumber = SiteSettings::whatsappNumber();
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('page.faq.kicker')"
        :title="__('page.faq.title')"
        :intro="SiteSettings::pageIntro('faq')"
    />

    <section class="band-base section-y">
        <div class="container-bader grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
            @if ($faqs->isNotEmpty())
                <div class="divide-y divide-hairline overflow-hidden rounded-3xl border border-hairline bg-white shadow-card-sm" data-reveal>
                    @foreach ($faqs as $faq)
                        <details class="group" @if ($loop->first) open @endif>
                            <summary class="flex cursor-pointer items-center gap-4 p-5 transition hover:bg-paper sm:p-6">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-sm font-extrabold text-forest-700 transition group-open:bg-forest-700 group-open:text-white">{{ $loop->iteration }}</span>
                                <span class="flex-1 text-base font-extrabold leading-7 text-ink-900 sm:text-lg">{{ $faq->question }}</span>
                                <x-bader.icon name="chevron-down" class="h-5 w-5 shrink-0 text-forest-700 transition-transform duration-300 group-open:rotate-180" />
                            </summary>
                            <div class="px-5 pb-6 sm:px-6 sm:ps-[4.75rem]">
                                <p class="whitespace-pre-line leading-8 text-muted">{{ $faq->answer }}</p>
                            </div>
                        </details>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-hairline-strong bg-white p-10 text-center" data-reveal>
                    <x-bader.icon name="info" class="mx-auto h-10 w-10 text-forest-600" />
                    <p class="mx-auto mt-4 max-w-md text-muted">{{ __('faq_page.empty') }}</p>
                </div>
            @endif

            <aside class="space-y-4 lg:sticky lg:top-28" data-reveal>
                <div class="relative isolate overflow-hidden rounded-3xl bg-teal-950 p-6 text-white shadow-card-md">
                    <div class="pointer-events-none absolute -end-16 -top-16 -z-10 h-48 w-48 rounded-full bg-forest-600/40 blur-3xl" aria-hidden="true"></div>
                    <x-bader.icon name="info" class="h-8 w-8 text-gold-400" />
                    <p class="mt-4 text-xl font-extrabold">{{ __('faq_page.more_title') }}</p>
                    <p class="mt-2 text-sm leading-7 text-white/75">{{ __('faq_page.more_text') }}</p>
                    <div class="mt-6 grid gap-3">
                        <a href="{{ route('contact') }}" class="btn-primary w-full">
                            <x-bader.icon name="mail" class="h-4 w-4" />
                            {{ __('faq_page.contact_cta') }}
                        </a>
                        @if ($whatsappNumber)
                            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" class="btn-ghost w-full">
                                <x-bader.icon name="whatsapp" class="h-4 w-4" />
                                {{ __('common.whatsapp_contact') }}
                            </a>
                        @endif
                    </div>
                </div>

                <nav class="rounded-3xl border border-hairline bg-white p-5" aria-label="{{ __('faq_page.related') }}">
                    <p class="text-sm font-extrabold text-ink-900">{{ __('faq_page.related') }}</p>
                    <ul class="mt-3 space-y-1">
                        @foreach (['about', 'sponsorship', 'zakat', 'impact'] as $route)
                            <li>
                                <a href="{{ route($route) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-ink-800 transition hover:bg-paper hover:text-forest-700">
                                    <x-bader.nav-icon :route="$route" class="h-4 w-4 text-forest-600" />
                                    {{ __('nav.'.$route) }}
                                    <x-bader.icon name="chevron-end" class="ms-auto h-4 w-4 text-subtle" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </aside>
        </div>
    </section>
@endsection
