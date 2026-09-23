@extends('layouts.dashboard')

@section('title', __('dashboard.module.stories') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  {{-- Page Header --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
        {{ __('dashboard.module.stories') }}
      </h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
        {{ __('dashboard.module.stories_desc') }}
      </p>
    </div>
    <a
      href="{{ route('dashboard.stories.create') }}"
      class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition"
    >
      <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      <span>{{ __('dashboard.add_new') }}</span>
    </a>
  </div>

  {{-- Flash message --}}
  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  {{-- Table --}}
  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-100 dark:border-gray-800">
            <th class="px-5 py-4 text-start text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
              {{ __('dashboard.field.title_ar') }}
            </th>
            <th class="px-5 py-4 text-start text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
              {{ __('dashboard.field.category_ar') }}
            </th>
            <th class="px-5 py-4 text-start text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
              {{ __('dashboard.field.published_at') }}
            </th>
            <th class="px-5 py-4 text-start text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
              {{ __('dashboard.field.featured') }}
            </th>
            <th class="px-5 py-4 text-start text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
              {{ __('dashboard.field.status') }}
            </th>
            <th class="px-5 py-4 text-end text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
              {{ __('dashboard.actions') }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse ($stories as $story)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
              <td class="px-5 py-4">
                <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $story->title_ar }}</p>
                @if ($story->title_en)
                  <p class="text-xs text-gray-400 dark:text-gray-600" dir="ltr">{{ $story->title_en }}</p>
                @endif
              </td>
              <td class="px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400">
                {{ $story->category_ar ?? '—' }}
              </td>
              <td class="px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400" dir="ltr">
                {{ $story->published_at ? \Carbon\Carbon::parse($story->published_at)->format('Y-m-d') : '—' }}
              </td>
              <td class="px-5 py-4">
                @if ($story->is_featured)
                  <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">{{ __('dashboard.yes') }}</span>
                @else
                  <span class="text-xs text-gray-400">—</span>
                @endif
              </td>
              <td class="px-5 py-4">
                @if ($story->status === 'published')
                  <span class="inline-flex items-center rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-400">{{ __('dashboard.status.published') }}</span>
                @elseif ($story->status === 'under_review')
                  <span class="inline-flex items-center rounded-full bg-warning-50 px-2.5 py-0.5 text-xs font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-400">{{ __('dashboard.status.under_review') }}</span>
                @else
                  <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ __('dashboard.status.draft') }}</span>
                @endif
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('dashboard.stories.edit', $story) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    {{ __('dashboard.edit') }}
                  </a>
                  <form action="{{ route('dashboard.stories.destroy', $story) }}" method="POST" onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="inline-flex items-center gap-1.5 rounded-lg border border-error-200 bg-error-50 px-3 py-2 text-theme-xs font-medium text-error-600 shadow-theme-xs hover:bg-error-100 dark:border-error-500/20 dark:bg-error-500/10 dark:text-error-400 dark:hover:bg-error-500/20">
                      <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      {{ __('dashboard.delete') }}
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-5 py-12 text-center text-theme-sm text-gray-400 dark:text-gray-600">
                {{ __('dashboard.no_records') }}
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($stories->hasPages())
      <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">
        {{ $stories->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
