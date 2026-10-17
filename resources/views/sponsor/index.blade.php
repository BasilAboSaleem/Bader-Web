@extends('layouts.dashboard')

@section('title', __('dashboard.module.my_sponsorships') . ' — ' . __('brand.name'))

@php
  use App\Support\SponsorshipAmounts;

  $card = 'rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6';
  $term = 'text-theme-xs text-gray-500 dark:text-gray-400';
  $detail = 'mt-0.5 text-theme-sm font-medium text-gray-800 dark:text-white/90';
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('sponsors.portal.welcome', ['name' => $sponsor?->name ?? auth()->user()->name]) }}</h1>
    <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('sponsors.portal.intro') }}</p>
  </div>

  @if ($activeSponsorships->isEmpty())
    <div class="{{ $card }} text-center">
      <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ __('sponsors.portal.empty') }}</p>
    </div>
  @else
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div class="{{ $card }}">
        <p class="{{ $term }}">{{ __('sponsors.portal.sponsored_count') }}</p>
        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $activeSponsorships->count() }}</p>
      </div>
      @foreach (SponsorshipAmounts::monthlyTotals($activeSponsorships) as $currency => $total)
        <div class="{{ $card }}">
          <p class="{{ $term }}">{{ __('sponsors.portal.monthly_total', ['currency' => __('sponsors.currency.'.$currency)]) }}</p>
          <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white" dir="ltr">{{ SponsorshipAmounts::format($total, $currency) }}</p>
        </div>
      @endforeach
    </div>

    @foreach ($activeSponsorships->groupBy('orphan_family_id') as $familySponsorships)
      @php $family = $familySponsorships->first()->family; @endphp
      <section class="{{ $card }}">
        <h2 class="mb-4 border-b border-gray-100 pb-3 text-base font-semibold text-gray-900 dark:border-gray-800 dark:text-white">
          {{ $family->isWidowFamily() ? __('sponsors.portal.widow_family', ['name' => $family->mother_full_name]) : __('sponsors.portal.orphan_file') }}
        </h2>
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          @foreach ($familySponsorships->sortBy(fn ($sponsorship) => $sponsorship->isForMother() ? 0 : 1) as $sponsorship)
            <article class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <h3 class="text-theme-sm font-semibold text-gray-900 dark:text-white">{{ $sponsorship->beneficiaryName() }}</h3>
                  <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ __('sponsors.beneficiary.'.$sponsorship->beneficiary_type) }}</p>
                </div>
                <span class="rounded-full bg-brand-50 px-3 py-1 text-theme-sm font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400" dir="ltr">
                  {{ SponsorshipAmounts::format($sponsorship->monthly_amount, $sponsorship->currency) }}
                </span>
              </div>
              <dl class="mt-4 grid grid-cols-2 gap-3">
                @if ($sponsorship->isForMother())
                  <div>
                    <dt class="{{ $term }}">{{ __('sponsors.portal.age') }}</dt>
                    <dd class="{{ $detail }}">{{ $family->mother_birth_date ? __('sponsorship.age_years', ['age' => $family->mother_birth_date->age]) : '—' }}</dd>
                  </div>
                @else
                  <div>
                    <dt class="{{ $term }}">{{ __('sponsors.portal.age') }}</dt>
                    <dd class="{{ $detail }}">{{ __('sponsorship.age_years', ['age' => $sponsorship->orphan->age]) }}</dd>
                  </div>
                  <div>
                    <dt class="{{ $term }}">{{ __('sponsors.portal.education') }}</dt>
                    <dd class="{{ $detail }}">{{ __('orphans.education.'.$sponsorship->orphan->education_stage) }}</dd>
                  </div>
                  <div>
                    <dt class="{{ $term }}">{{ __('sponsors.portal.health') }}</dt>
                    <dd class="{{ $detail }}">{{ __('orphans.health.'.$sponsorship->orphan->health_status) }}</dd>
                  </div>
                @endif
                <div>
                  <dt class="{{ $term }}">{{ __('sponsors.portal.since') }}</dt>
                  <dd class="{{ $detail }}" dir="ltr">{{ $sponsorship->started_at->format('Y-m-d') }}</dd>
                </div>
              </dl>
            </article>
          @endforeach
        </div>
      </section>
    @endforeach
  @endif

  @if ($endedSponsorships->isNotEmpty())
    <section class="{{ $card }}">
      <h2 class="mb-4 border-b border-gray-100 pb-3 text-base font-semibold text-gray-900 dark:border-gray-800 dark:text-white">{{ __('sponsors.history_title') }}</h2>
      <ul class="divide-y divide-gray-100 dark:divide-gray-800">
        @foreach ($endedSponsorships as $sponsorship)
          <li class="flex flex-wrap items-center justify-between gap-2 py-3 text-theme-sm">
            <span class="font-medium text-gray-800 dark:text-white/90">{{ $sponsorship->beneficiaryName() }}</span>
            <span class="text-theme-xs text-gray-500 dark:text-gray-400">
              <span dir="ltr">{{ SponsorshipAmounts::format($sponsorship->monthly_amount, $sponsorship->currency) }}</span>
              —
              <span dir="ltr">{{ $sponsorship->started_at->format('Y-m-d') }} / {{ $sponsorship->ended_at->format('Y-m-d') }}</span>
            </span>
          </li>
        @endforeach
      </ul>
    </section>
  @endif

  <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ __('sponsors.portal.reports_soon') }}</p>
</div>
@endsection
