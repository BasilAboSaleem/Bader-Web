@extends('layouts.public')

@section('title', __('page.donate.title').' — '.__('brand.name'))

@section('content')
    <section class="bg-bader-green-deep text-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
            <x-bader.section-heading :kicker="__('page.donate.kicker')" :title="__('page.donate.title')" theme="dark" />
            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base">{{ __('page.donate.intro') }}</p>
        </div>
    </section>

    <section class="bg-bader-paper">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-14 lg:grid-cols-[1.2fr_0.8fr]">
            <article class="rounded-3xl bg-bader-green p-6 text-white sm:p-8" data-reveal>
                <span class="inline-flex rounded-full bg-bader-lime px-3 py-1 text-xs font-semibold text-bader-green-deep">{{ __('campaign.status_active') }}</span>
                <h2 class="mt-5 text-2xl font-semibold">{{ __('campaign.'.$campaignKey) }}</h2>
                <p class="mt-3 text-sm leading-relaxed text-white/80">{{ __('campaign.'.$campaignKey.'_text') }}</p>
                <p class="mt-6 text-sm text-white/70">{{ __('campaign.goal') }} <strong class="text-lg text-bader-lime">120,000 {{ __('campaign.currency') }}</strong></p>
            </article>
            <aside class="rounded-2xl border border-bader-green/10 bg-white p-6" data-reveal>
                <h2 class="text-xl font-semibold text-bader-green">{{ __('page.donate.manual_title') }}</h2>
                <p class="mt-3 text-sm leading-relaxed text-bader-ink/70">{{ __('page.donate.manual_text') }}</p>
                <a href="{{ route('contact') }}" class="mt-6 inline-block text-sm font-semibold text-bader-green">{{ __('nav.contact') }}</a>
            </aside>
        </div>
    </section>

    {{-- Bank Transfer Notification Form --}}
    <section class="border-t border-bader-green/10 bg-white py-16">
        <div class="mx-auto max-w-3xl px-4">
            <x-bader.section-heading :kicker="__('donation.transfer_kicker')" :title="__('donation.transfer_heading')" />
            <p class="mt-3 text-sm text-bader-ink/70">{{ __('donation.transfer_subheading') }}</p>

            @if (session('success_message'))
                <div class="mt-6 rounded-2xl border border-bader-green/20 bg-bader-paper p-5 text-sm font-semibold text-bader-green-deep">
                    {{ session('success_message') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('donate.transfer.submit') }}" class="mt-8 space-y-5" data-reveal>
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.donor_name') }}</label>
                        <input type="text" name="donor_name" value="{{ old('donor_name') }}" placeholder="{{ __('donation.anonymous') }}"
                               class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.phone') }}</label>
                        <input type="text" name="donor_phone" value="{{ old('donor_phone') }}" dir="ltr"
                               class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.amount') }} *</label>
                        <input type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" required placeholder="e.g. 50"
                               class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.reference_number') }} *</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number') }}" required dir="ltr" placeholder="Bank receipt ref #"
                               class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.donor_email') }}</label>
                        <input type="email" name="donor_email" value="{{ old('donor_email') }}" dir="ltr"
                               class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.transfer_date') }}</label>
                        <input type="date" name="transfer_date" value="{{ old('transfer_date', now()->toDateString()) }}"
                               class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:bg-white focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.notes') }}</label>
                    <textarea name="notes" rows="3" class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper p-4 text-sm text-bader-ink focus:border-bader-green focus:bg-white focus:outline-none">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-bader-green px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-bader-green-deep transition">
                        {{ __('form.submit_transfer_notice') }}
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
