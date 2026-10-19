@extends('layouts.dashboard')

@section('title', __('sponsors.disbursements.record') . ' — ' . __('brand.name'))

@php
  use App\Support\SponsorshipAmounts;
  $inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-theme-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('sponsors.disbursements.record') }}</h1>
    <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('sponsors.disbursements.record_hint') }}</p>
  </div>

  <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <label class="block">
      <span class="mb-1 block text-theme-xs text-gray-500">{{ __('sponsors.disbursements.year') }}</span>
      <input type="number" name="year" value="{{ $year }}" min="2000" max="2100" class="{{ $inputClass }} w-28">
    </label>
    <label class="block">
      <span class="mb-1 block text-theme-xs text-gray-500">{{ __('sponsors.disbursements.month') }}</span>
      <select name="month" class="{{ $inputClass }} w-28">
        @for ($m = 1; $m <= 12; $m++)
          <option value="{{ $m }}" @selected($month === $m)>{{ $m }}</option>
        @endfor
      </select>
    </label>
    <button type="submit" class="h-11 rounded-lg bg-gray-900 px-4 text-theme-sm font-medium text-white dark:bg-white dark:text-gray-900">{{ __('dashboard.filter') }}</button>
  </form>

  @if ($sponsorships->isEmpty())
    <div class="rounded-2xl border border-gray-200 bg-white p-6 text-theme-sm text-gray-500 dark:border-gray-800 dark:bg-white/[0.03]">{{ __('sponsors.no_active') }}</div>
  @else
    <form action="{{ route('dashboard.disbursements.store') }}" method="POST" class="space-y-4">
      @csrf
      <input type="hidden" name="period_year" value="{{ $year }}">
      <input type="hidden" name="period_month" value="{{ $month }}">
      <label class="block max-w-xs">
        <span class="mb-1 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('sponsors.disbursements.paid_on') }}</span>
        <input type="date" name="paid_on" value="{{ old('paid_on', now()->toDateString()) }}" required class="{{ $inputClass }}">
        @error('paid_on') <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p> @enderror
      </label>

      <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 dark:border-gray-800">
              <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.disbursements.include') }}</th>
              <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.column.beneficiary') }}</th>
              <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.column.sponsor') }}</th>
              <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.column.monthly_amount') }}</th>
              <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.disbursements.notes') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach ($sponsorships as $index => $sponsorship)
              @php $paid = $existing->get($sponsorship->id); @endphp
              <tr class="{{ $paid ? 'bg-success-50/40 dark:bg-success-500/5' : '' }}">
                <td class="px-3 py-3 text-center align-middle">
                  <input type="checkbox" name="rows[{{ $index }}][sponsorship_id]" value="{{ $sponsorship->id }}"
                    @checked(old('rows.'.$index.'.sponsorship_id', $paid ? $sponsorship->id : null) == $sponsorship->id)
                    class="size-4 rounded border-gray-300 text-brand-500">
                  <input type="hidden" name="rows[{{ $index }}][currency]" value="{{ $sponsorship->currency }}">
                </td>
                <td class="px-3 py-3 text-center align-middle text-theme-sm font-medium text-gray-800 dark:text-white/90">
                  {{ $sponsorship->beneficiaryName() }}
                  <span class="block font-mono text-theme-xs text-gray-500" dir="ltr">{{ $sponsorship->family->code }}</span>
                </td>
                <td class="px-3 py-3 text-center align-middle text-theme-sm text-gray-600 dark:text-gray-300">{{ $sponsorship->sponsor->name }}</td>
                <td class="px-3 py-3 text-center align-middle">
                  <input type="number" name="rows[{{ $index }}][amount]" value="{{ old('rows.'.$index.'.amount', $paid->amount ?? $sponsorship->monthly_amount) }}" min="0.01" max="100000" step="0.01" dir="ltr" class="{{ $inputClass }} mx-auto w-32">
                </td>
                <td class="px-3 py-3 text-center align-middle">
                  <input type="text" name="rows[{{ $index }}][notes]" value="{{ old('rows.'.$index.'.notes', $paid->notes ?? '') }}" class="{{ $inputClass }} mx-auto">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @error('rows') <p class="text-theme-xs text-error-600">{{ $message }}</p> @enderror

      <button type="submit" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('dashboard.save_changes') }}</button>
    </form>
  @endif
</div>
@endsection
