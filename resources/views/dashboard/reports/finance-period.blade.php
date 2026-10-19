@extends('layouts.dashboard')

@section('title', __('sponsors.report.finance_period') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('sponsors.report.finance_period') }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('sponsors.report.finance_month_hint') }}</p>
    </div>
    <a href="{{ route('dashboard.reports.finance.print', ['year' => $year, 'month' => $month]) }}" target="_blank" rel="noopener"
      class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('sponsors.report.print') }}</a>
  </div>

  <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <label class="block">
      <span class="mb-1 block text-theme-xs text-gray-500">{{ __('sponsors.disbursements.year') }}</span>
      <input type="number" name="year" value="{{ $year }}" min="2000" max="2100" class="h-11 w-28 rounded-lg border border-gray-300 bg-transparent px-3 dark:border-gray-700 dark:bg-gray-900">
    </label>
    <label class="block">
      <span class="mb-1 block text-theme-xs text-gray-500">{{ __('sponsors.disbursements.month') }}</span>
      <select name="month" class="h-11 rounded-lg border border-gray-300 bg-transparent px-3 dark:border-gray-700 dark:bg-gray-900">
        @for ($m = 1; $m <= 12; $m++)
          <option value="{{ $m }}" @selected($month === $m)>{{ $m }}</option>
        @endfor
      </select>
    </label>
    <button type="submit" class="h-11 rounded-lg bg-gray-900 px-4 text-theme-sm font-medium text-white dark:bg-white dark:text-gray-900">{{ __('dashboard.filter') }}</button>
  </form>

  @include('reports.partials.month-spreadsheet', [
      'year' => $year,
      'month' => $month,
      'rows' => $rows,
      'totals' => $totals,
      'print' => false,
  ])
</div>
@endsection
