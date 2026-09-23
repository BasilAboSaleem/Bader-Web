@extends('layouts.dashboard')

@section('title', __('dashboard.module.donations') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">
  {{-- Header & Stats --}}
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('dashboard.module.donations') }}</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.donations_subtitle') }}</p>
    </div>
    <a href="{{ route('dashboard.donations.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-brand-600 transition">
      <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      {{ __('dashboard.record_offline_donation') }}
    </a>
  </div>

  @if (session('status'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/20 dark:text-emerald-300">
      {{ session('status') }}
    </div>
  @endif

  {{-- Summary Cards --}}
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
      <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.total_verified_donations') }}</p>
      <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($totalVerified, 2) }} <span class="text-xs font-medium text-gray-500">{{ __('brand.currency') }}</span></p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
      <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.pending_verification') }}</p>
      <p class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $pendingCount }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
      <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.donation_method_rule') }}</p>
      <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 leading-relaxed">{{ __('dashboard.donation_method_rule_text') }}</p>
    </div>
  </div>

  {{-- Filter bar --}}
  <div class="flex flex-wrap items-center gap-3">
    <a href="{{ route('dashboard.donations.index') }}"
       class="rounded-lg px-3.5 py-1.5 text-xs font-medium transition {{ empty($status) ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300' }}">
      {{ __('dashboard.all') }}
    </a>
    <a href="{{ route('dashboard.donations.index', ['status' => 'verified']) }}"
       class="rounded-lg px-3.5 py-1.5 text-xs font-medium transition {{ $status === 'verified' ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300' }}">
      {{ __('dashboard.status_verified') }}
    </a>
    <a href="{{ route('dashboard.donations.index', ['status' => 'pending']) }}"
       class="rounded-lg px-3.5 py-1.5 text-xs font-medium transition {{ $status === 'pending' ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300' }}">
      {{ __('dashboard.status_pending') }}
    </a>
  </div>

  {{-- Table Card --}}
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-start text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
          <tr>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.donor') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.campaign') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.amount') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.method_ref') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.date') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.status') }}</th>
            <th class="px-6 py-4 text-end font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse ($donations as $item)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
              <td class="px-6 py-4">
                <div class="font-medium text-gray-900 dark:text-white">{{ $item->donor_name ?? __('donation.anonymous') }}</div>
                @if ($item->donor_phone)
                  <div class="text-xs text-gray-500 dark:text-gray-400" dir="ltr">{{ $item->donor_phone }}</div>
                @endif
              </td>
              <td class="px-6 py-4">
                @if ($item->campaign)
                  <span class="font-medium text-brand-600 dark:text-brand-400">{{ $item->campaign->title_ar }}</span>
                @else
                  <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.general_donation') }}</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($item->amount, 2) }}</span>
                <span class="text-xs text-gray-500">{{ $item->currency_ar }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ __('dashboard.payment_method_'.$item->payment_method) }}</div>
                @if ($item->reference_number)
                  <div class="text-[11px] font-mono text-gray-500 dark:text-gray-400" dir="ltr">{{ $item->reference_number }}</div>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                {{ $item->transfer_date?->format('Y-m-d') ?? $item->created_at->format('Y-m-d') }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                @if ($item->status === 'verified')
                  <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                    {{ __('dashboard.status_verified') }}
                  </span>
                @elseif ($item->status === 'pending')
                  <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                    {{ __('dashboard.status_pending') }}
                  </span>
                @else
                  <span class="inline-flex rounded-full bg-error-50 px-2.5 py-1 text-xs font-semibold text-error-700 dark:bg-error-950/40 dark:text-error-400">
                    {{ __('dashboard.status_rejected') }}
                  </span>
                @endif
              </td>
              <td class="px-6 py-4 text-end whitespace-nowrap">
                <div class="flex items-center justify-end gap-2">
                  @if ($item->status === 'pending')
                    <form method="POST" action="{{ route('dashboard.donations.verify', $item) }}">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-emerald-700 transition">
                        {{ __('dashboard.verify_donation') }}
                      </button>
                    </form>
                  @endif

                  <a href="{{ route('dashboard.donations.edit', $item) }}"
                     class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </a>

                  <form method="POST" action="{{ route('dashboard.donations.destroy', $item) }}" onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg border border-error-200 bg-white p-1.5 text-error-600 hover:bg-error-50 dark:border-error-800/40 dark:bg-gray-800 dark:text-error-400 dark:hover:bg-error-950/20">
                      <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                {{ __('dashboard.no_records') }}
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($donations->hasPages())
      <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        {{ $donations->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
