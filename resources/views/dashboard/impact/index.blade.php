@extends('layouts.dashboard')

@section('title', __('dashboard.module.impact') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">
  {{-- Header --}}
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('dashboard.module.impact') }}</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.impact_subtitle') }}</p>
    </div>
    <a href="{{ route('dashboard.impact.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-brand-600 transition">
      <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      {{ __('dashboard.add_metric') }}
    </a>
  </div>

  @if (session('status'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/20 dark:text-emerald-300">
      {{ session('status') }}
    </div>
  @endif

  {{-- Metrics Notice Alert --}}
  <div class="rounded-xl border border-brand-200 bg-brand-50/60 p-4 text-sm text-brand-900 dark:border-brand-900/40 dark:bg-brand-950/20 dark:text-brand-300">
    <div class="flex items-center gap-2 font-semibold">
      <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      {{ __('dashboard.impact_approval_rule_title') }}
    </div>
    <p class="mt-1 text-xs text-brand-800/80 dark:text-brand-300/80 leading-relaxed">
      {{ __('dashboard.impact_approval_rule_text') }}
    </p>
  </div>

  {{-- Metrics Table --}}
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-start text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
          <tr>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.order') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.title') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.metric_value') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.category') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.approval_status') }}</th>
            <th class="px-6 py-4 text-end font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse ($metrics as $metric)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
              <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-gray-400">
                #{{ $metric->order }}
              </td>
              <td class="px-6 py-4">
                <div class="font-medium text-gray-900 dark:text-white">{{ $metric->title_ar }}</div>
                @if ($metric->title_en)
                  <div class="text-xs text-gray-400">{{ $metric->title_en }}</div>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-base font-bold text-brand-600 dark:text-brand-400">{{ $metric->value }}</span>
                @if ($metric->unit_ar)
                  <span class="text-xs text-gray-500 dark:text-gray-400">{{ $metric->unit_ar }}</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                {{ $metric->category ?? '—' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                @if ($metric->is_approved)
                  <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                    {{ __('dashboard.metric_status_approved') }}
                  </span>
                @else
                  <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                    <span class="size-1.5 rounded-full bg-amber-500"></span>
                    {{ __('dashboard.metric_status_unapproved') }}
                  </span>
                @endif
              </td>
              <td class="px-6 py-4 text-end whitespace-nowrap">
                <div class="flex items-center justify-end gap-2">
                  <form method="POST" action="{{ route('dashboard.impact.toggle', $metric) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-lg border px-2.5 py-1 text-xs font-medium transition {{ $metric->is_approved ? 'border-amber-200 text-amber-700 hover:bg-amber-50 dark:border-amber-800 dark:text-amber-400' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50 dark:border-emerald-800 dark:text-emerald-400' }}">
                      {{ $metric->is_approved ? __('dashboard.unapprove_btn') : __('dashboard.approve_btn') }}
                    </button>
                  </form>

                  <a href="{{ route('dashboard.impact.edit', $metric) }}"
                     class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </a>

                  <form method="POST" action="{{ route('dashboard.impact.destroy', $metric) }}" onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
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
              <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                {{ __('dashboard.no_records') }}
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($metrics->hasPages())
      <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        {{ $metrics->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
