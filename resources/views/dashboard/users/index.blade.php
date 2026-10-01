@extends('layouts.dashboard')

@section('title', __('dashboard.module.users') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('dashboard.module.users') }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.module.users_desc') }}</p>
    </div>
    <a href="{{ route('dashboard.users.create') }}"
      class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
      <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      <span>{{ __('dashboard.users_add') }}</span>
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
            @foreach (['dashboard.users_name', 'dashboard.users_email', 'dashboard.users_since'] as $heading)
              <th class="px-5 py-4 text-start text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __($heading) }}</th>
            @endforeach
            <th class="px-5 py-4 text-end text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('dashboard.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse ($users as $user)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
              <td class="px-5 py-4 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</td>
              <td class="px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400" dir="ltr">{{ $user->email }}</td>
              <td class="px-5 py-4 text-theme-sm text-gray-500 dark:text-gray-400">{{ $user->created_at?->translatedFormat('j F Y') }}</td>
              <td class="px-5 py-4">
                <form action="{{ route('dashboard.users.destroy', $user) }}" method="POST" class="flex justify-end" onsubmit="return confirm('{{ __('dashboard.users_confirm_delete') }}')">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-error-200 bg-error-50 px-3 py-2 text-theme-xs font-medium text-error-600 shadow-theme-xs hover:bg-error-100 dark:border-error-500/20 dark:bg-error-500/10 dark:text-error-400 dark:hover:bg-error-500/20">
                    {{ __('dashboard.users_remove') }}
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-5 py-12 text-center text-theme-sm text-gray-400 dark:text-gray-600">{{ __('dashboard.users_empty') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
