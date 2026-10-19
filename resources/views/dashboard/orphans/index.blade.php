@extends('layouts.dashboard')

@section('title', __('dashboard.module.orphans') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('dashboard.module.orphans') }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.module.orphans_desc') }}</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
      <a href="{{ route('orphan-registration') }}" target="_blank" rel="noopener"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
        {{ __('orphans.public_form_link') }}
      </a>
      <a href="{{ route('dashboard.orphans.import') }}"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L9 8m4-4v12"/></svg>
        <span>{{ __('orphans.import.action') }}</span>
      </a>
      <a href="{{ route('dashboard.orphans.create') }}"
        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>{{ __('orphans.add_manually') }}</span>
      </a>
    </div>
  </div>

  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap gap-2">
      @foreach (\App\Models\OrphanFamily::REVIEW_STATUSES as $status)
        <a href="{{ route('dashboard.orphans.index', ['status' => $status]) }}"
          class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-theme-xs font-medium {{ $currentStatus === $status ? 'bg-brand-500 text-white' : 'border border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-400' }}">
          {{ __('orphans.review_tab.'.$status) }}
          <span class="rounded-full bg-black/10 px-1.5 text-[11px]">{{ $statusCounts[$status] ?? 0 }}</span>
        </a>
      @endforeach
    </div>
    <form method="GET" action="{{ route('dashboard.orphans.index') }}" class="flex w-full items-center gap-2 sm:w-auto">
      <input type="hidden" name="status" value="{{ $currentStatus }}">
      <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('orphans.search_placeholder') }}" aria-label="{{ __('orphans.search_placeholder') }}"
        class="h-10 min-w-0 flex-1 sm:w-64 sm:flex-none rounded-lg border border-gray-300 bg-transparent px-3 text-theme-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
      <button type="submit" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-theme-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('orphans.search') }}</button>
    </form>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-100 dark:border-gray-800">
            @foreach (['orphans.column.code', 'orphans.column.name', 'orphans.column.type', 'orphans.column.orphans_count', 'orphans.column.source', 'orphans.column.status', 'orphans.column.submitted_at'] as $heading)
              <th class="px-5 py-4 text-start text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __($heading) }}</th>
            @endforeach
            <th class="px-5 py-4 text-end text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('dashboard.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse ($families as $family)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
              <td class="px-5 py-4 font-mono text-theme-xs text-gray-600 dark:text-gray-300" dir="ltr">{{ $family->code }}</td>
              <td class="px-5 py-4 text-theme-sm font-medium text-gray-800 dark:text-white/90">
                <a href="{{ route('dashboard.orphans.show', $family) }}" class="hover:text-brand-500">{{ $family->display_name }}</a>
              </td>
              <td class="px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('orphans.type.'.$family->type) }}</td>
              <td class="px-5 py-4 text-theme-sm text-gray-700 dark:text-gray-300">{{ $family->orphans_count }}</td>
              <td class="px-5 py-4 text-theme-xs text-gray-500 dark:text-gray-400">{{ __('orphans.source.'.$family->source) }}</td>
              <td class="px-5 py-4">
                <x-dashboard.orphan-status :status="$family->currentStatus()" />
              </td>
              <td class="px-5 py-4 text-theme-xs text-gray-500 dark:text-gray-400" dir="ltr">{{ $family->created_at->format('Y-m-d') }}</td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('dashboard.orphans.show', $family) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-2 text-theme-xs font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    {{ $family->review_status === \App\Models\OrphanFamily::REVIEW_PENDING ? __('orphans.review_action') : __('orphans.view') }}
                  </a>
                  <a href="{{ route('dashboard.orphans.edit', $family) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    {{ __('dashboard.edit') }}
                  </a>
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

    @if ($families->hasPages())
      <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">{{ $families->links() }}</div>
    @endif
  </div>

</div>
@endsection
