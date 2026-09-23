@extends('layouts.dashboard')

@section('title', __('dashboard.inbox_detail_title') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">
  {{-- Header --}}
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <div class="flex items-center gap-3">
        <a href="{{ route('dashboard.inbox.index') }}" class="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-400">
          <svg class="size-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('dashboard.inbox_type_'.$submission->type) }}</h1>
      </div>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        {{ __('dashboard.field.submitted_at') }}: {{ $submission->created_at->format('Y-m-d H:i:s') }}
      </p>
    </div>

    <form method="POST" action="{{ route('dashboard.inbox.destroy', $submission) }}" onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
      @csrf
      @method('DELETE')
      <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-error-200 bg-white px-4 py-2 text-sm font-medium text-error-600 hover:bg-error-50 dark:border-error-800/40 dark:bg-gray-800 dark:text-error-400 dark:hover:bg-error-950/20">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        {{ __('dashboard.delete') }}
      </button>
    </form>
  </div>

  @if (session('status'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/20 dark:text-emerald-300">
      {{ session('status') }}
    </div>
  @endif

  <div class="grid gap-6 lg:grid-cols-3">
    {{-- Left: Submission Content (2 cols) --}}
    <div class="space-y-6 lg:col-span-2">
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
        <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ __('dashboard.inbox_sender_info') }}</h2>
        
        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
          <div>
            <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.field.name') }}</dt>
            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $submission->name }}</dd>
          </div>
          <div>
            <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.field.email') }}</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $submission->email ?? '—' }}</dd>
          </div>
          <div>
            <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.field.phone') }}</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white" dir="ltr">{{ $submission->phone ?? '—' }}</dd>
          </div>
          @if ($submission->organization)
            <div>
              <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.field.organization') }}</dt>
              <dd class="mt-1 text-sm text-brand-600 dark:text-brand-400">{{ $submission->organization }}</dd>
            </div>
          @endif
          @if ($submission->location)
            <div>
              <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.field.location') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $submission->location }}</dd>
            </div>
          @endif
          @if ($submission->subject)
            <div class="sm:col-span-2">
              <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.field.subject') }}</dt>
              <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $submission->subject }}</dd>
            </div>
          @endif
        </dl>

        @if ($submission->message)
          <div class="mt-6 border-t border-gray-100 pt-6 dark:border-gray-800">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.field.message') }}</h3>
            <div class="mt-2 rounded-xl bg-gray-50 p-4 text-sm leading-relaxed text-gray-800 dark:bg-gray-800/60 dark:text-gray-200 whitespace-pre-wrap">
              {{ $submission->message }}
            </div>
          </div>
        @endif

        {{-- Dynamic Payload Details --}}
        @if ($submission->payload && count($submission->payload) > 0)
          <div class="mt-6 border-t border-gray-100 pt-6 dark:border-gray-800">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('dashboard.field.extra_details') }}</h3>
            <div class="mt-3 grid gap-3 sm:grid-cols-2">
              @foreach ($submission->payload as $pKey => $pValue)
                <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-gray-800/30">
                  <span class="block text-[11px] font-semibold uppercase text-gray-400">{{ __('dashboard.field.'.$pKey) ?? $pKey }}</span>
                  <span class="mt-0.5 block text-sm font-medium text-gray-800 dark:text-gray-200">
                    {{ is_array($pValue) ? json_encode($pValue, JSON_UNESCAPED_UNICODE) : ($pValue ?? '—') }}
                  </span>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>

    {{-- Right: Status & Notes (1 col) --}}
    <div class="space-y-6">
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
        <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ __('dashboard.inbox_status_management') }}</h2>
        
        <form method="POST" action="{{ route('dashboard.inbox.update', $submission) }}" class="mt-5 space-y-4">
          @csrf
          @method('PUT')

          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.status') }}</label>
            <select name="status" class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
              <option value="unread" {{ old('status', $submission->status) === 'unread' ? 'selected' : '' }}>{{ __('dashboard.status_unread') }}</option>
              <option value="in_progress" {{ old('status', $submission->status) === 'in_progress' ? 'selected' : '' }}>{{ __('dashboard.status_in_progress') }}</option>
              <option value="resolved" {{ old('status', $submission->status) === 'resolved' ? 'selected' : '' }}>{{ __('dashboard.status_resolved') }}</option>
              <option value="archived" {{ old('status', $submission->status) === 'archived' ? 'selected' : '' }}>{{ __('dashboard.status_archived') }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.internal_notes') }}</label>
            <textarea name="notes" rows="4" class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200" placeholder="{{ __('dashboard.notes_placeholder') }}">{{ old('notes', $submission->notes) }}</textarea>
          </div>

          <button type="submit" class="w-full rounded-lg bg-brand-500 py-2.5 text-center text-sm font-semibold text-white shadow-theme-xs hover:bg-brand-600 transition">
            {{ __('dashboard.save_changes') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
