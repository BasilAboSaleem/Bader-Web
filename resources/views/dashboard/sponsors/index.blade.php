@extends('layouts.dashboard')

@section('title', __('dashboard.module.sponsors') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('dashboard.module.sponsors') }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.module.sponsors_desc') }}</p>
    </div>
    <a href="{{ route('dashboard.sponsors.create') }}"
      class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
      <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      <span>{{ __('sponsors.add') }}</span>
    </a>
  </div>

  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  <form method="GET" action="{{ route('dashboard.sponsors.index') }}" class="flex w-full items-center gap-2 sm:w-auto">
    <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('sponsors.search_placeholder') }}" aria-label="{{ __('sponsors.search_placeholder') }}"
      class="h-10 min-w-0 flex-1 rounded-lg border border-gray-300 bg-transparent px-3 text-theme-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 sm:w-72 sm:flex-none">
    <button type="submit" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-theme-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('orphans.search') }}</button>
  </form>

  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-100 dark:border-gray-800">
            @foreach (['code', 'name', 'contact', 'active_sponsorships', 'account'] as $column)
              <th class="px-5 py-4 text-start text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('sponsors.column.'.$column) }}</th>
            @endforeach
            <th class="px-5 py-4 text-end text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('dashboard.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse ($sponsors as $sponsor)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
              <td class="px-5 py-4 font-mono text-theme-xs text-gray-600 dark:text-gray-300" dir="ltr">{{ $sponsor->code }}</td>
              <td class="px-5 py-4 text-theme-sm font-medium text-gray-800 dark:text-white/90">
                <a href="{{ route('dashboard.sponsors.show', $sponsor) }}" class="hover:text-brand-500">{{ $sponsor->name }}</a>
                @if ($sponsor->country)
                  <span class="block text-theme-xs font-normal text-gray-500 dark:text-gray-400">{{ $sponsor->country }}</span>
                @endif
              </td>
              <td class="px-5 py-4 text-theme-xs text-gray-600 dark:text-gray-300" dir="ltr">
                {{ $sponsor->email ?: '—' }}
                @if ($sponsor->phone)
                  <span class="block">{{ $sponsor->phone }}</span>
                @endif
              </td>
              <td class="px-5 py-4 text-theme-sm text-gray-700 dark:text-gray-300">{{ $sponsor->active_sponsorships_count }}</td>
              <td class="px-5 py-4">
                @if ($sponsor->user_id)
                  <span class="inline-flex rounded-full bg-success-50 px-2.5 py-0.5 text-theme-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500">{{ __('sponsors.account_active') }}</span>
                @else
                  <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-theme-xs font-medium text-gray-600 dark:bg-white/5 dark:text-gray-400">{{ __('sponsors.account_none') }}</span>
                @endif
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('dashboard.sponsors.show', $sponsor) }}"
                    class="inline-flex items-center rounded-lg bg-brand-500 px-3 py-2 text-theme-xs font-medium text-white shadow-theme-xs hover:bg-brand-600">{{ __('orphans.view') }}</a>
                  <a href="{{ route('dashboard.sponsors.edit', $sponsor) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('dashboard.edit') }}</a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-5 py-12 text-center text-theme-sm text-gray-400 dark:text-gray-600">{{ __('dashboard.no_records') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($sponsors->hasPages())
      <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">{{ $sponsors->links() }}</div>
    @endif
  </div>

</div>
@endsection
