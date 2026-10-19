@extends('layouts.dashboard')

@section('title', __('sponsors.report.finance_case') . ' — ' . $family->code . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('sponsors.report.finance_case') }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500">
        <a href="{{ route('dashboard.orphans.show', $family) }}" class="hover:text-brand-500" dir="ltr">{{ $family->code }}</a>
      </p>
    </div>
    <a href="{{ route('dashboard.orphans.finance.print', $family) }}" target="_blank" rel="noopener"
      class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('sponsors.report.print') }}</a>
  </div>

  @include('reports.partials.case-finance-body', [
      'family' => $family,
      'sponsorName' => $sponsorName,
      'approvedAmounts' => $approvedAmounts,
      'monthRows' => $monthRows,
      'totals' => $totals,
      'print' => false,
  ])
</div>
@endsection
