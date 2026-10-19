@extends('layouts.dashboard')

@section('title', __('sponsors.report.finance_case') . ' — ' . $sponsorship->beneficiaryName())

@section('content')
<div class="space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <nav class="mb-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('sponsor.home') }}" class="hover:text-brand-500">{{ __('dashboard.module.my_sponsorships') }}</a>
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('sponsor.show', $sponsorship) }}" class="hover:text-brand-500">{{ $sponsorship->beneficiaryName() }}</a>
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>{{ __('sponsors.report.finance_case') }}</span>
      </nav>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('sponsors.report.finance_case') }}</h1>
    </div>
    <a href="{{ route('sponsor.finance.print', $sponsorship) }}" target="_blank" rel="noopener"
      class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('sponsors.report.print') }}</a>
  </div>

  @include('reports.partials.finance-table', [
      'disbursements' => $disbursements,
      'totals' => $totals,
      'showBeneficiary' => false,
      'showSponsor' => false,
  ])
</div>
@endsection
