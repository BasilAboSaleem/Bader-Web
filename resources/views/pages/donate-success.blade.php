@extends('layouts.public')

@use('App\Support\Money')

@section('title', __('donate_success.title').' — '.__('brand.name'))

@php
    $locale = app()->getLocale();
    $isSettled = $donation && in_array($donation->status, ['completed', 'verified'], true);
    $giftCard = $donation?->giftCard();
@endphp

@section('content')
    <section class="band-tint relative isolate overflow-hidden py-10 sm:py-14">
        <div class="pointer-events-none absolute -end-24 -top-24 -z-10 h-80 w-80 rounded-full bg-gold-300/40 blur-3xl print:hidden" aria-hidden="true"></div>

        <div class="container-bader max-w-3xl">
            @if ($donation)
                <div class="text-center">
                    <span class="mx-auto flex h-20 w-20 animate-fade-up items-center justify-center rounded-full {{ $isSettled ? 'bg-gold-500 text-teal-950' : 'bg-forest-700 text-white' }} shadow-card-lg">
                        <x-bader.icon :name="$isSettled ? 'check' : 'clock'" class="h-10 w-10" />
                    </span>
                    <h1 class="mt-6 animate-fade-up text-3xl font-extrabold text-ink-900 [animation-delay:80ms] sm:text-4xl">
                        {{ $isSettled ? __('donate_success.title') : __('donate_success.pending_title') }}
                    </h1>
                    <p class="mx-auto mt-3 max-w-xl animate-fade-up leading-relaxed text-muted [animation-delay:160ms]">
                        {{ $isSettled ? __('donate_success.text') : __('donate_success.pending_text') }}
                    </p>
                </div>

                <div class="surface-card mt-10 overflow-hidden" data-receipt>
                    <div class="flex items-center justify-between gap-4 border-b border-hairline bg-paper-2 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ \App\Support\SiteSettings::brandAsset('mark_star') }}" alt="" class="h-8 w-8">
                            <div>
                                <p class="font-extrabold text-ink-900">{{ __('donate_success.receipt_title') }}</p>
                                <p class="text-xs text-subtle">{{ __('brand.name') }}</p>
                            </div>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $isSettled ? 'bg-forest-700 text-white' : 'bg-gold-500 text-teal-950' }}">{{ __('donate_success.status_'.($isSettled ? 'settled' : 'pending')) }}</span>
                    </div>
                    <dl class="divide-y divide-hairline px-6 text-sm">
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-subtle">{{ __('donate_success.receipt_number') }}</dt>
                            <dd class="font-mono font-bold text-ink-900" dir="ltr">{{ $donation->receipt_number }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-subtle">{{ __('donate_success.date') }}</dt>
                            <dd class="font-bold text-ink-900">{{ $donation->created_at->locale($locale)->translatedFormat('j F Y — H:i') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-subtle">{{ __('donation.amount_label') }}</dt>
                            <dd class="text-lg font-extrabold text-forest-700" dir="ltr">{{ Money::format($donation->amount) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-subtle">{{ __('donation.frequency_label') }}</dt>
                            <dd class="font-bold text-ink-900">{{ __('donation.frequency.'.($donation->isMonthly() ? 'monthly' : 'once')) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-subtle">{{ __('donate_success.target') }}</dt>
                            <dd class="text-end font-bold text-ink-900">{{ $donation->target_title }}</dd>
                        </div>
                        @if ($donation->donation_category)
                            <div class="flex justify-between gap-4 py-3">
                                <dt class="text-subtle">{{ __('donate_page.category') }}</dt>
                                <dd class="font-bold text-ink-900">{{ \App\Support\SiteSettings::donationCategoryLabel($donation->donation_category) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-subtle">{{ __('donate_success.donor') }}</dt>
                            <dd class="font-bold text-ink-900">{{ $donation->display_name }}</dd>
                        </div>
                        @if ($donation->payment_method && \Illuminate\Support\Facades\Lang::has('donation.method.'.$donation->payment_method))
                            <div class="flex justify-between gap-4 py-3">
                                <dt class="text-subtle">{{ __('donate_success.payment_method') }}</dt>
                                <dd class="font-bold text-ink-900">{{ __('donation.method.'.$donation->payment_method) }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                @if ($giftCard)
                    <div class="mt-6">
                        <p class="mb-3 text-sm font-bold text-subtle">{{ __('donate_success.gift_card') }}</p>
                        <div class="gift-card gift-card-preview" style="--gift-from: {{ $giftCard['from'] ?? '#0a2e2f' }}; --gift-to: {{ $giftCard['to'] ?? '#1f6b38' }}; --gift-accent: {{ $giftCard['accent'] ?? '#e1e56b' }}">
                            <div class="flex items-center justify-between">
                                <img src="{{ \App\Support\SiteSettings::brandAsset('mark_star') }}" alt="" class="h-9 w-9">
                                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold">{{ $giftCard['label_'.$locale] ?? $giftCard['label_ar'] }}</span>
                            </div>
                            <p class="mt-8 text-sm opacity-80">{{ __('gift_page.preview_to') }}</p>
                            <p class="mt-1 text-2xl font-extrabold text-[var(--gift-accent)]">{{ $donation->gift_recipient_name }}</p>
                            @if ($donation->gift_message)
                                <p class="mt-5 text-sm leading-relaxed opacity-90">{{ $donation->gift_message }}</p>
                            @endif
                            <div class="mt-6 flex items-end justify-between gap-3 border-t border-white/20 pt-4 text-sm">
                                <div>
                                    <p class="text-xs opacity-70">{{ __('gift_page.preview_from') }}</p>
                                    <p class="font-bold">{{ $donation->gift_sender_name ?: $donation->display_name }}</p>
                                </div>
                                <p class="text-xs opacity-80">{{ __('donate_success.gift_in_name') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-8 flex flex-wrap justify-center gap-3 print:hidden">
                    <button type="button" class="btn-outline" data-print>
                        <x-bader.icon name="printer" class="h-4 w-4" />
                        {{ __('donate_success.print') }}
                    </button>
                    <button type="button" class="btn-outline" data-share data-share-url="{{ route('home') }}" data-share-title="{{ __('brand.name') }}" data-copied-text="{{ __('common.link_copied') }}">
                        <x-bader.icon name="share" class="h-4 w-4" />
                        <span data-share-label>{{ __('donate_success.share') }}</span>
                    </button>
                    <a href="{{ route('home') }}" class="btn-brand">{{ __('donate_success.home') }}</a>
                </div>
            @else
                <div class="surface-card p-10 text-center">
                    <x-bader.icon name="info" class="mx-auto h-12 w-12 text-forest-600" />
                    <h1 class="mt-5 text-2xl font-extrabold text-ink-900">{{ __('donate_success.not_found_title') }}</h1>
                    <p class="mt-3 text-muted">{{ __('donate_success.not_found_text') }}</p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('contact') }}" class="btn-outline">{{ __('nav.contact') }}</a>
                        <a href="{{ route('donate') }}" class="btn-primary">{{ __('nav.donate') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
