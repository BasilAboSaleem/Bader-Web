@extends('layouts.dashboard')

@section('title', __('dashboard.module.sponsorship_cases') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('dashboard.module.sponsorship_cases') }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.module.sponsorship_cases_desc') }}</p>
    </div>
    <a href="{{ route('dashboard.sponsorship-cases.create') }}"
      class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
      <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      <span>{{ __('dashboard.add_new') }}</span>
    </a>
  </div>

  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  <div class="flex flex-wrap gap-2">
    <a href="{{ route('dashboard.sponsorship-cases.index') }}"
      class="rounded-full px-3.5 py-1.5 text-theme-xs font-medium {{ $currentStatus ? 'border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400' : 'bg-brand-500 text-white' }}">{{ __('dashboard.all') }}</a>
    @foreach ($statuses as $status)
      <a href="{{ route('dashboard.sponsorship-cases.index', ['status' => $status]) }}"
        class="rounded-full px-3.5 py-1.5 text-theme-xs font-medium {{ $currentStatus === $status ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400' }}">{{ __('sponsorship.status.'.$status) }}</a>
    @endforeach
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-100 dark:border-gray-800">
            @foreach (['dashboard.field.code', 'dashboard.field.name_ar', 'dashboard.field.case_type', 'dashboard.field.region', 'dashboard.field.monthly_amount', 'dashboard.field.waiting_since', 'dashboard.field.status'] as $heading)
              <th class="px-5 py-4 text-start text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __($heading) }}</th>
            @endforeach
            <th class="px-5 py-4 text-end text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('dashboard.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse ($cases as $case)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
              <td class="px-5 py-4 font-mono text-theme-xs text-gray-600 dark:text-gray-300" dir="ltr">{{ $case->code }}</td>
              <td class="px-5 py-4 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $case->name_ar }}</td>
              <td class="px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400">{{ $case->type_label }}</td>
              <td class="px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400">{{ $case->region?->name_ar ?? '—' }}</td>
              <td class="px-5 py-4 text-theme-sm text-gray-700 dark:text-gray-300" dir="ltr">${{ number_format((float) $case->monthly_amount, 2) }}</td>
              <td class="px-5 py-4 text-theme-xs text-gray-500 dark:text-gray-400">{{ $case->waiting_since?->format('Y-m-d') ?? '—' }}</td>
              <td class="px-5 py-4">
                @php
                  $badge = match ($case->status) {
                    'available' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
                    'sponsored' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
                    default => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                  };
                @endphp
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">{{ __('sponsorship.status.'.$case->status) }}</span>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('dashboard.sponsorship-cases.edit', $case) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    {{ __('dashboard.edit') }}
                  </a>
                  <form action="{{ route('dashboard.sponsorship-cases.destroy', $case) }}" method="POST" onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="inline-flex items-center gap-1.5 rounded-lg border border-error-200 bg-error-50 px-3 py-2 text-theme-xs font-medium text-error-600 shadow-theme-xs hover:bg-error-100 dark:border-error-500/20 dark:bg-error-500/10 dark:text-error-400 dark:hover:bg-error-500/20">
                      {{ __('dashboard.delete') }}
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-5 py-12 text-center text-theme-sm text-gray-400 dark:text-gray-600">{{ __('dashboard.no_records') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($cases->hasPages())
      <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">{{ $cases->links() }}</div>
    @endif
  </div>

</div>
@endsection
