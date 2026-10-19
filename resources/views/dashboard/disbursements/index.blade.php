@extends('layouts.dashboard')

@section('title', __('dashboard.module.disbursements') . ' — ' . __('brand.name'))

@php
  use App\Support\SponsorshipAmounts;
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('dashboard.module.disbursements') }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.module.disbursements_desc') }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('dashboard.reports.finance') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('sponsors.report.finance_period') }}</a>
      <a href="{{ route('dashboard.disbursements.create', ['year' => $year, 'month' => $month]) }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('sponsors.disbursements.record') }}</a>
    </div>
  </div>

  @if (session('status'))
    <div class="rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">{{ session('status') }}</div>
  @endif

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

  @include('reports.partials.finance-table', [
      'disbursements' => $disbursements,
      'totals' => $totals,
      'showBeneficiary' => true,
      'showSponsor' => true,
      'showFile' => true,
  ])

  @foreach ($disbursements as $row)
    <form id="delete-disbursement-{{ $row->id }}" action="{{ route('dashboard.disbursements.destroy', $row) }}" method="POST" class="hidden">
      @csrf
      @method('DELETE')
    </form>
  @endforeach

  @if ($disbursements->isNotEmpty())
    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
      <p class="mb-3 text-theme-sm font-medium text-gray-700 dark:text-gray-300">{{ __('sponsors.disbursements.manage') }}</p>
      <ul class="divide-y divide-gray-100 dark:divide-gray-800">
        @foreach ($disbursements as $row)
          <li class="flex flex-wrap items-center justify-between gap-2 py-2 text-theme-sm">
            <span>{{ $row->sponsorship->beneficiaryName() }} — <span dir="ltr">{{ $row->periodLabel() }}</span></span>
            <button type="submit" form="delete-disbursement-{{ $row->id }}" onclick="return confirm('{{ __('dashboard.confirm_delete') }}')" class="text-error-600 hover:underline">{{ __('dashboard.delete') }}</button>
          </li>
        @endforeach
      </ul>
    </div>
  @endif
</div>
@endsection
