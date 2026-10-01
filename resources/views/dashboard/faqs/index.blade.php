@extends('layouts.dashboard')

@section('title', __('dashboard.module.faqs') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('dashboard.module.faqs') }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.module.faqs_desc') }}</p>
    </div>
    <a href="{{ route('dashboard.faqs.create') }}"
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

  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-100 dark:border-gray-800">
            @foreach (['dashboard.question_ar', 'dashboard.question_en', 'dashboard.field.order', 'dashboard.field.status'] as $heading)
              <th class="px-5 py-4 text-start text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __($heading) }}</th>
            @endforeach
            <th class="px-5 py-4 text-end text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('dashboard.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse ($faqs as $faq)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
              <td class="max-w-sm px-5 py-4 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $faq->question_ar }}</td>
              <td class="max-w-sm px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400" dir="ltr">{{ $faq->question_en ?? '—' }}</td>
              <td class="px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400">{{ $faq->order }}</td>
              <td class="px-5 py-4">
                @if ($faq->status === 'published')
                  <span class="inline-flex items-center rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-400">{{ __('dashboard.status.published') }}</span>
                @else
                  <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ __('dashboard.status.draft') }}</span>
                @endif
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('dashboard.faqs.edit', $faq) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    {{ __('dashboard.edit') }}
                  </a>
                  <form action="{{ route('dashboard.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
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
              <td colspan="5" class="px-5 py-12 text-center text-theme-sm text-gray-400 dark:text-gray-600">{{ __('dashboard.no_records') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($faqs->hasPages())
      <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">{{ $faqs->links() }}</div>
    @endif
  </div>

</div>
@endsection
