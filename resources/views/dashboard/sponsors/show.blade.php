@extends('layouts.dashboard')

@section('title', $sponsor->name . ' — ' . __('dashboard.module.sponsors') . ' — ' . __('brand.name'))

@php
  use App\Support\SponsorshipAmounts;

  $card = 'rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6';
  $cardTitle = 'mb-4 border-b border-gray-100 pb-3 text-base font-semibold text-gray-900 dark:border-gray-800 dark:text-white';
  $term = 'text-theme-xs text-gray-500 dark:text-gray-400';
  $detail = 'mt-0.5 text-theme-sm font-medium text-gray-800 dark:text-white/90';
  $activeSponsorships = $sponsor->sponsorships->whereNull('ended_at');
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ $sponsor->name }}</h1>
      <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('dashboard.sponsors.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.sponsors') }}</a>
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span dir="ltr">{{ $sponsor->code }}</span>
      </nav>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('dashboard.sponsors.edit', $sponsor) }}"
        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('dashboard.edit') }}</a>
      @if ($sponsor->sponsorships->isEmpty())
        <form action="{{ route('dashboard.sponsors.destroy', $sponsor) }}" method="POST" onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
          @csrf
          @method('DELETE')
          <button type="submit" class="inline-flex items-center rounded-lg border border-error-200 bg-error-50 px-4 py-2.5 text-theme-sm font-medium text-error-600 hover:bg-error-100 dark:border-error-500/20 dark:bg-error-500/10 dark:text-error-400">{{ __('dashboard.delete') }}</button>
        </form>
      @endif
    </div>
  </div>

  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  @error('sponsor')
    <div class="rounded-xl border border-error-500/20 bg-error-50 p-4 text-theme-sm font-medium text-error-700 dark:bg-error-500/10 dark:text-error-400" role="alert">{{ $message }}</div>
  @enderror

  @if (session('invitation_link'))
    <div class="rounded-xl border border-brand-200 bg-brand-50 p-4 dark:border-brand-500/30 dark:bg-brand-500/10" data-copy-scope>
      <p class="text-theme-sm font-semibold text-brand-700 dark:text-brand-300">{{ __('sponsors.invitation_title') }}</p>
      <p class="mt-1 text-theme-xs text-brand-700/80 dark:text-brand-300/80">{{ __('sponsors.invitation_hint') }}</p>
      <div class="mt-3 flex flex-col gap-2 sm:flex-row">
        <input type="text" readonly value="{{ session('invitation_link') }}" dir="ltr" data-copy-source
          class="h-10 min-w-0 flex-1 rounded-lg border border-brand-200 bg-white px-3 text-theme-xs text-gray-700 dark:border-brand-500/30 dark:bg-gray-900 dark:text-gray-300" onclick="this.select()">
        <button type="button" data-copy-button data-copied-label="{{ __('sponsors.copied') }}"
          class="h-10 rounded-lg bg-brand-500 px-4 text-theme-sm font-medium text-white hover:bg-brand-600"
          onclick="navigator.clipboard.writeText(this.closest('[data-copy-scope]').querySelector('[data-copy-source]').value).then(() => { this.textContent = this.dataset.copiedLabel; })">{{ __('sponsors.copy_link') }}</button>
      </div>
    </div>
  @endif

  <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
    <div class="min-w-0 space-y-6">
      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('sponsors.active_title') }} ({{ $activeSponsorships->count() }})</h2>
        @if ($activeSponsorships->isEmpty())
          <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ __('sponsors.no_active') }}</p>
        @else
          <div class="mb-4 flex flex-wrap gap-2">
            @foreach (SponsorshipAmounts::monthlyTotals($activeSponsorships) as $currency => $total)
              <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-theme-xs font-semibold text-gray-700 dark:bg-white/5 dark:text-gray-300">
                {{ __('sponsors.monthly_total') }}: <span dir="ltr">{{ SponsorshipAmounts::format($total, $currency) }}</span>
              </span>
            @endforeach
          </div>
          <x-dashboard.sponsorships-table :sponsorships="$activeSponsorships" />
        @endif
      </div>

      @php $endedSponsorships = $sponsor->sponsorships->whereNotNull('ended_at'); @endphp
      @if ($endedSponsorships->isNotEmpty())
        <div class="{{ $card }}">
          <h2 class="{{ $cardTitle }}">{{ __('sponsors.history_title') }} ({{ $endedSponsorships->count() }})</h2>
          <x-dashboard.sponsorships-table :sponsorships="$endedSponsorships" />
        </div>
      @endif
    </div>

    <aside class="space-y-6">
      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('sponsors.details_title') }}</h2>
        <dl class="space-y-3">
          @foreach (['email', 'phone', 'country'] as $field)
            <div>
              <dt class="{{ $term }}">{{ __('sponsors.field.'.$field) }}</dt>
              <dd class="{{ $detail }}" @if ($field !== 'country') dir="ltr" @endif>{{ $sponsor->{$field} ?: '—' }}</dd>
            </div>
          @endforeach
          <div>
            <dt class="{{ $term }}">{{ __('sponsors.field.preferred_currency') }}</dt>
            <dd class="{{ $detail }}">{{ __('sponsors.currency.'.$sponsor->preferred_currency) }}</dd>
          </div>
          @if ($sponsor->notes)
            <div>
              <dt class="{{ $term }}">{{ __('sponsors.field.notes') }}</dt>
              <dd class="{{ $detail }} whitespace-pre-line font-normal">{{ $sponsor->notes }}</dd>
            </div>
          @endif
        </dl>
      </div>

      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('sponsors.account_title') }}</h2>
        @if ($sponsor->user)
          <p class="text-theme-sm text-gray-700 dark:text-gray-300">{{ __('sponsors.account_active_hint') }}</p>
          <form action="{{ route('dashboard.sponsors.invite', $sponsor) }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('sponsors.resend_invitation') }}</button>
          </form>
        @else
          <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ __('sponsors.account_none_hint') }}</p>
        @endif
      </div>
    </aside>
  </div>
</div>
@endsection
