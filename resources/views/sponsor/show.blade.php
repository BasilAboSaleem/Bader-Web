@extends('layouts.dashboard')

@section('title', $sponsorship->beneficiaryName() . ' — ' . __('dashboard.module.my_sponsorships') . ' — ' . __('brand.name'))

@php
  use App\Models\SponsorRequest;
  use App\Support\SponsorshipAmounts;

  $card = 'rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6';
  $cardTitle = 'mb-4 border-b border-gray-100 pb-3 text-base font-semibold text-gray-900 dark:border-gray-800 dark:text-white';
  $term = 'text-theme-xs text-gray-500 dark:text-gray-400';
  $detail = 'mt-0.5 text-theme-sm font-medium text-gray-800 dark:text-white/90';
  $date = fn ($value) => $value?->format('Y-m-d') ?? '—';
  $inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
@endphp

@section('content')
<div class="space-y-6">
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <nav class="mb-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('sponsor.home') }}" class="hover:text-brand-500">{{ __('dashboard.module.my_sponsorships') }}</a>
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>{{ $sponsorship->beneficiaryName() }}</span>
      </nav>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ $sponsorship->beneficiaryName() }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
        {{ __('sponsors.beneficiary.'.$sponsorship->beneficiary_type) }}
        — <span dir="ltr">{{ SponsorshipAmounts::format($sponsorship->monthly_amount, $sponsorship->currency) }}</span>
        @unless ($sponsorship->isActive())
          — {{ __('sponsors.history_title') }}
        @endunless
      </p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('sponsor.print', $sponsorship) }}" target="_blank" rel="noopener"
        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('sponsors.report.print_case') }}</a>
      <a href="{{ route('sponsor.finance', $sponsorship) }}"
        class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('sponsors.report.finance_case') }}</a>
    </div>
  </div>

  @if (session('status'))
    <div class="rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">{{ session('status') }}</div>
  @endif

  <div class="{{ $card }}">
    <h2 class="{{ $cardTitle }}">{{ __('sponsors.portal.case_file') }}</h2>
    @include('sponsor.partials.case-tables', ['family' => $family, 'sponsorship' => $sponsorship])
  </div>

  <div class="{{ $card }}">
    <h2 class="{{ $cardTitle }}">{{ __('sponsors.disbursements.title') }}</h2>
    @if ($sponsorship->disbursements->isEmpty())
      <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ __('sponsors.disbursements.empty') }}</p>
    @else
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 dark:border-gray-800">
              <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.disbursements.period') }}</th>
              <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.disbursements.paid_on') }}</th>
              <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.column.monthly_amount') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach ($sponsorship->disbursements->take(12) as $row)
              <tr>
                <td class="px-3 py-3 text-center align-middle text-theme-sm text-gray-800 dark:text-white/90" dir="ltr">{{ $row->periodLabel() }}</td>
                <td class="px-3 py-3 text-center align-middle text-theme-sm text-gray-600" dir="ltr">{{ $row->paid_on->format('Y-m-d') }}</td>
                <td class="px-3 py-3 text-center align-middle text-theme-sm font-semibold" dir="ltr">{{ SponsorshipAmounts::format($row->amount, $row->currency) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <a href="{{ route('sponsor.finance', $sponsorship) }}" class="mt-3 inline-block text-theme-sm font-medium text-brand-500 hover:underline">{{ __('sponsors.disbursements.view_all') }}</a>
    @endif
  </div>

  @if ($sponsorship->isActive())
    <div class="{{ $card }}">
      <h2 class="{{ $cardTitle }}">{{ __('sponsors.requests.title') }}</h2>
      @if ($pendingRequest)
        <p class="text-theme-sm text-warning-700 dark:text-warning-400">
          {{ __('sponsors.requests.pending_notice', ['type' => __('sponsors.requests.types.'.$pendingRequest->type)]) }}
        </p>
      @else
        <form action="{{ route('sponsor.requests.store', $sponsorship) }}" method="POST" class="space-y-4">
          @csrf
          <label class="block">
            <span class="mb-1 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('sponsors.requests.type') }}</span>
            <select name="type" id="request_type" class="{{ $inputClass }}">
              @foreach (SponsorRequest::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type') === $type)>{{ __('sponsors.requests.types.'.$type) }}</option>
              @endforeach
            </select>
          </label>
          <label class="block" id="amount_wrap">
            <span class="mb-1 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('sponsors.field.monthly_amount') }}</span>
            <input type="number" name="requested_amount" value="{{ old('requested_amount') }}" min="1" max="100000" step="0.01" dir="ltr" class="{{ $inputClass }}">
            @error('requested_amount') <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p> @enderror
          </label>
          <label class="block">
            <span class="mb-1 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('sponsors.requests.message') }}</span>
            <textarea name="message" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm dark:border-gray-700 dark:bg-gray-900">{{ old('message') }}</textarea>
          </label>
          @error('type') <p class="text-theme-xs text-error-600">{{ $message }}</p> @enderror
          <button type="submit" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('sponsors.requests.submit') }}</button>
        </form>
        <script>
          (() => {
            const type = document.getElementById('request_type');
            const wrap = document.getElementById('amount_wrap');
            const sync = () => { wrap.hidden = type.value === 'stop'; };
            type.addEventListener('change', sync);
            sync();
          })();
        </script>
      @endif
    </div>
  @endif
</div>
@endsection
