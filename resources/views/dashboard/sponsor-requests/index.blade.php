@extends('layouts.dashboard')

@section('title', __('dashboard.module.sponsor_requests') . ' — ' . __('brand.name'))

@php
  use App\Models\SponsorRequest;
  use App\Support\SponsorshipAmounts;
@endphp

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('dashboard.module.sponsor_requests') }}</h1>
    <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.module.sponsor_requests_desc') }}</p>
  </div>

  @if (session('status'))
    <div class="rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">{{ session('status') }}</div>
  @endif

  <div class="flex flex-wrap gap-2">
    @foreach ([SponsorRequest::STATUS_PENDING, SponsorRequest::STATUS_APPROVED, SponsorRequest::STATUS_REJECTED] as $tab)
      <a href="{{ route('dashboard.sponsor-requests.index', ['status' => $tab]) }}"
        class="rounded-lg px-3 py-2 text-theme-sm font-medium {{ $status === $tab ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-700 dark:border-gray-700 dark:text-gray-300' }}">
        {{ __('sponsors.requests.status.'.$tab) }}
        @if (($statusCounts[$tab] ?? 0) > 0)
          <span class="ms-1">({{ $statusCounts[$tab] }})</span>
        @endif
      </a>
    @endforeach
  </div>

  <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <table class="w-full">
      <thead>
        <tr class="border-b border-gray-100 dark:border-gray-800">
          <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.column.sponsor') }}</th>
          <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.column.beneficiary') }}</th>
          <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.requests.type') }}</th>
          <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.field.monthly_amount') }}</th>
          <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500">{{ __('sponsors.requests.message') }}</th>
          <th class="px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse ($requests as $item)
          <tr>
            <td class="px-3 py-3 text-center align-middle text-theme-sm">{{ $item->sponsor->name }}</td>
            <td class="px-3 py-3 text-center align-middle text-theme-sm">
              {{ $item->sponsorship->beneficiaryName() }}
              <a href="{{ route('dashboard.orphans.show', $item->sponsorship->family) }}" class="block font-mono text-theme-xs text-brand-500" dir="ltr">{{ $item->sponsorship->family->code }}</a>
            </td>
            <td class="px-3 py-3 text-center align-middle text-theme-sm">{{ __('sponsors.requests.types.'.$item->type) }}</td>
            <td class="px-3 py-3 text-center align-middle text-theme-sm" dir="ltr">
              @if ($item->type === SponsorRequest::TYPE_STOP)
                —
              @else
                {{ SponsorshipAmounts::format($item->requested_amount, $item->currency ?: $item->sponsorship->currency) }}
              @endif
            </td>
            <td class="px-3 py-3 text-center align-middle text-theme-sm text-gray-500">{{ $item->message ?: '—' }}</td>
            <td class="px-3 py-3 text-center align-middle text-theme-sm">
              @if ($item->isPending())
                <form action="{{ route('dashboard.sponsor-requests.update', $item) }}" method="POST" class="flex flex-wrap items-center justify-center gap-2">
                  @csrf
                  @method('PATCH')
                  <input type="text" name="admin_notes" placeholder="{{ __('sponsors.requests.admin_notes') }}" class="h-9 rounded-lg border border-gray-300 px-2 text-theme-xs dark:border-gray-700 dark:bg-gray-900">
                  <button type="submit" name="decision" value="{{ SponsorRequest::STATUS_APPROVED }}" class="rounded-lg bg-success-600 px-3 py-1.5 text-theme-xs font-medium text-white">{{ __('sponsors.requests.approve') }}</button>
                  <button type="submit" name="decision" value="{{ SponsorRequest::STATUS_REJECTED }}" class="rounded-lg border border-error-200 bg-error-50 px-3 py-1.5 text-theme-xs font-medium text-error-600">{{ __('sponsors.requests.reject') }}</button>
                </form>
              @else
                <span class="text-theme-xs text-gray-500">{{ __('sponsors.requests.status.'.$item->status) }}</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-3 py-8 text-center text-theme-sm text-gray-500">{{ __('sponsors.requests.empty') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $requests->links() }}
</div>
@endsection
