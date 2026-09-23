@extends('layouts.dashboard')

@section('title', __('dashboard.module.inbox') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">
  {{-- Header & Stats --}}
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('dashboard.module.inbox') }}</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.inbox_subtitle') }}</p>
    </div>
  </div>

  @if (session('status'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/20 dark:text-emerald-300">
      {{ session('status') }}
    </div>
  @endif

  {{-- Filter Tabs --}}
  <div class="flex flex-wrap items-center gap-2 border-b border-gray-200 pb-4 dark:border-gray-800">
    <a href="{{ route('dashboard.inbox.index') }}"
       class="inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium transition {{ empty($type) ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
      <span>{{ __('dashboard.inbox_all') }}</span>
      <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $counts['all'] }}</span>
    </a>
    <a href="{{ route('dashboard.inbox.index', ['type' => 'contact']) }}"
       class="inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium transition {{ $type === 'contact' ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
      <span>{{ __('dashboard.inbox_contact') }}</span>
      <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $counts['contact'] }}</span>
    </a>
    <a href="{{ route('dashboard.inbox.index', ['type' => 'partnership']) }}"
       class="inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium transition {{ $type === 'partnership' ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
      <span>{{ __('dashboard.inbox_partnership') }}</span>
      <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $counts['partnership'] }}</span>
    </a>
    <a href="{{ route('dashboard.inbox.index', ['type' => 'volunteer']) }}"
       class="inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium transition {{ $type === 'volunteer' ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
      <span>{{ __('dashboard.inbox_volunteer') }}</span>
      <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $counts['volunteer'] }}</span>
    </a>
    <a href="{{ route('dashboard.inbox.index', ['type' => 'sponsorship']) }}"
       class="inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium transition {{ $type === 'sponsorship' ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
      <span>{{ __('dashboard.inbox_sponsorship') }}</span>
      <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $counts['sponsorship'] }}</span>
    </a>
    <a href="{{ route('dashboard.inbox.index', ['type' => 'assistance']) }}"
       class="inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium transition {{ $type === 'assistance' ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
      <span>{{ __('dashboard.inbox_assistance') }}</span>
      <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $counts['assistance'] }}</span>
    </a>
    <a href="{{ route('dashboard.inbox.index', ['type' => 'donation_transfer']) }}"
       class="inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium transition {{ $type === 'donation_transfer' ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
      <span>{{ __('dashboard.inbox_transfer') }}</span>
      <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $counts['donation_transfer'] }}</span>
    </a>
  </div>

  {{-- Submissions Table Card --}}
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-start text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
          <tr>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.sender') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.type') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.subject_message') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.date') }}</th>
            <th class="px-6 py-4 text-start font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.status') }}</th>
            <th class="px-6 py-4 text-end font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse ($submissions as $item)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
              <td class="px-6 py-4">
                <div class="font-medium text-gray-900 dark:text-white">{{ $item->name }}</div>
                @if ($item->email)
                  <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->email }}</div>
                @endif
                @if ($item->phone)
                  <div class="text-xs text-gray-500 dark:text-gray-400" dir="ltr">{{ $item->phone }}</div>
                @endif
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                  {{ __('dashboard.inbox_type_'.$item->type) }}
                </span>
                @if ($item->organization)
                  <div class="mt-1 text-xs text-brand-600 dark:text-brand-400">{{ $item->organization }}</div>
                @endif
              </td>
              <td class="px-6 py-4">
                @if ($item->subject)
                  <div class="font-medium text-gray-800 dark:text-gray-200">{{ $item->subject }}</div>
                @endif
                <div class="max-w-xs truncate text-xs text-gray-500 dark:text-gray-400">
                  {{ $item->message ?? '—' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                {{ $item->created_at->format('Y-m-d H:i') }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                @if ($item->status === 'unread')
                  <span class="inline-flex rounded-full bg-error-50 px-2.5 py-1 text-xs font-semibold text-error-700 dark:bg-error-950/40 dark:text-error-400">
                    {{ __('dashboard.status_unread') }}
                  </span>
                @elseif ($item->status === 'in_progress')
                  <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                    {{ __('dashboard.status_in_progress') }}
                  </span>
                @elseif ($item->status === 'resolved')
                  <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                    {{ __('dashboard.status_resolved') }}
                  </span>
                @else
                  <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ __('dashboard.status_archived') }}
                  </span>
                @endif
              </td>
              <td class="px-6 py-4 text-end whitespace-nowrap">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('dashboard.inbox.show', $item) }}"
                     class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    {{ __('dashboard.view_details') }}
                  </a>
                  <form method="POST" action="{{ route('dashboard.inbox.destroy', $item) }}" onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
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

    @if ($submissions->hasPages())
      <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        {{ $submissions->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
