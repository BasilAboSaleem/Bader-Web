@extends('layouts.dashboard')

@section('title', $family->code . ' — ' . __('dashboard.module.orphans') . ' — ' . __('brand.name'))

@php
  use App\Models\OrphanFamily;

  $canManageOrphans = $canManageOrphans ?? auth()->user()?->canManageOrphans() ?? false;
  $canManageFinance = $canManageFinance ?? auth()->user()?->canManageFinance() ?? false;
  $card = 'rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6';
  $cardTitle = 'mb-4 border-b border-gray-100 pb-3 text-base font-semibold text-gray-900 dark:border-gray-800 dark:text-white';
  $term = 'text-theme-xs text-gray-500 dark:text-gray-400';
  $detail = 'mt-0.5 text-theme-sm font-medium text-gray-800 dark:text-white/90';
  $inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
  $date = fn ($value) => $value?->format('Y-m-d') ?? '—';
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="flex flex-wrap items-center gap-3 text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
        <span dir="ltr">{{ $family->code }}</span>
        <x-dashboard.orphan-status :status="$family->currentStatus()" />
      </h1>
      <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('dashboard.orphans.index', ['status' => $family->review_status]) }}" class="hover:text-brand-500">{{ __('dashboard.module.orphans') }}</a>
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>{{ __('orphans.type.'.$family->type) }} — {{ __('orphans.source.'.$family->source) }} — <span dir="ltr">{{ $family->created_at->format('Y-m-d H:i') }}</span></span>
      </nav>
    </div>
    <div class="flex flex-wrap items-center gap-2">
      <a href="{{ route('dashboard.orphans.print', $family) }}" target="_blank" rel="noopener"
        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('sponsors.report.print_case') }}</a>
      <a href="{{ route('dashboard.orphans.finance', $family) }}"
        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('sponsors.report.finance_case') }}</a>
      @if ($canManageOrphans)
        <a href="{{ route('dashboard.orphans.edit', $family) }}"
          class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('dashboard.edit') }}</a>
        <form action="{{ route('dashboard.orphans.destroy', $family) }}" method="POST" onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
          @csrf
          @method('DELETE')
          <button type="submit" class="inline-flex items-center rounded-lg border border-error-200 bg-error-50 px-4 py-2.5 text-theme-sm font-medium text-error-600 hover:bg-error-100 dark:border-error-500/20 dark:bg-error-500/10 dark:text-error-400">{{ __('dashboard.delete') }}</button>
        </form>
      @endif
    </div>
  </div>

  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  @error('sponsorship')
    <div class="rounded-xl border border-error-500/20 bg-error-50 p-4 text-theme-sm font-medium text-error-700 dark:bg-error-500/10 dark:text-error-400" role="alert">{{ $message }}</div>
  @enderror

  @if ($duplicates !== [])
    <div class="rounded-xl border border-warning-500/30 bg-warning-50 p-4 text-theme-sm text-warning-800 dark:bg-warning-500/10 dark:text-warning-400" role="alert">
      <p class="font-semibold">{{ __('orphans.duplicates_title') }}</p>
      <ul class="mt-1 list-disc space-y-1 ps-5">
        @foreach ($duplicates as $number => $codes)
          <li><span dir="ltr">{{ $number }}</span> — {{ __('orphans.duplicates_in', ['codes' => implode('، ', $codes)]) }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
    <div class="min-w-0 space-y-6">

      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('sponsors.report.form_heading') }}</h2>
        @include('sponsor.partials.case-tables', [
            'family' => $family,
            'sponsorship' => $family->activeSponsorships->first() ?? $family->sponsorships->sortByDesc('started_at')->first(),
            'showOrphanPhotos' => true,
        ])
      </div>

      @if ($family->activeSponsorships->isNotEmpty())
        <div class="{{ $card }}">
          <h2 class="{{ $cardTitle }}">{{ __('sponsors.active_title') }} ({{ $family->activeSponsorships->count() }})</h2>
          <x-dashboard.sponsorships-table :sponsorships="$family->activeSponsorships" show-sponsor />
        </div>
      @endif

      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('sponsors.report.finance_case') }}</h2>
        @include('reports.partials.case-finance-body', [
            'family' => $family,
            'sponsorName' => $sponsorName ?? '—',
            'approvedAmounts' => $approvedAmounts ?? collect(),
            'monthRows' => $monthRows ?? collect(),
            'totals' => $financeTotals ?? collect(),
            'print' => false,
        ])
      </div>

      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('orphans.section.documents') }}</h2>
        <ul class="grid grid-cols-1 gap-3 sm:grid-cols-3">
          @foreach (OrphanFamily::DOCUMENTS as $document)
            <li class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
              <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ __('orphans.document.'.$document) }}</p>
              @if ($family->hasDocument($document))
                <a href="{{ route('dashboard.orphans.document', [$family, $document]) }}" target="_blank" rel="noopener" class="mt-2 inline-block text-theme-sm font-medium text-brand-500 hover:underline">{{ __('orphans.open_document') }}</a>
              @else
                <p class="mt-2 text-theme-xs text-error-600">{{ __('orphans.document_missing') }}</p>
              @endif
            </li>
          @endforeach
        </ul>
      </div>
    </div>

    <aside class="space-y-6">
      @if ($canManageOrphans)
      <form action="{{ route('dashboard.orphans.review', $family) }}" method="POST" class="{{ $card }}">
        @csrf
        @method('PATCH')
        <h2 class="{{ $cardTitle }}">{{ __('orphans.review_title') }}</h2>
        @if ($family->reviewed_at)
          <p class="mb-3 text-theme-xs text-gray-500 dark:text-gray-400">
            {{ __('orphans.reviewed_by', ['name' => $family->reviewer?->name ?? '—']) }} — <span dir="ltr">{{ $family->reviewed_at->format('Y-m-d H:i') }}</span>
          </p>
        @endif
        <label for="review_notes" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('orphans.review_notes') }}</label>
        <textarea id="review_notes" name="review_notes" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('review_notes', $family->review_notes) }}</textarea>
        <p class="mt-2 text-theme-xs text-gray-500 dark:text-gray-400">{{ __('orphans.approve_hint') }}</p>
        <div class="mt-4 flex flex-wrap gap-2">
          <button type="submit" name="review_status" value="{{ OrphanFamily::REVIEW_APPROVED }}" @disabled($family->isApproved())
            class="inline-flex flex-1 items-center justify-center rounded-lg bg-success-600 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-success-700 disabled:opacity-50">{{ __('orphans.approve') }}</button>
          <button type="submit" name="review_status" value="{{ OrphanFamily::REVIEW_REJECTED }}" @disabled($family->review_status === OrphanFamily::REVIEW_REJECTED)
            class="inline-flex flex-1 items-center justify-center rounded-lg border border-error-200 bg-error-50 px-4 py-2.5 text-theme-sm font-medium text-error-600 hover:bg-error-100 disabled:opacity-50 dark:border-error-500/20 dark:bg-error-500/10 dark:text-error-400">{{ __('orphans.reject') }}</button>
        </div>
      </form>
      @elseif ($family->reviewed_at || $family->review_notes)
        <div class="{{ $card }}">
          <h2 class="{{ $cardTitle }}">{{ __('orphans.review_title') }}</h2>
          @if ($family->reviewed_at)
            <p class="mb-2 text-theme-xs text-gray-500 dark:text-gray-400">
              {{ __('orphans.reviewed_by', ['name' => $family->reviewer?->name ?? '—']) }} — <span dir="ltr">{{ $family->reviewed_at->format('Y-m-d H:i') }}</span>
            </p>
          @endif
          <p class="text-theme-sm text-gray-700 dark:text-gray-300">{{ $family->review_notes ?: '—' }}</p>
        </div>
      @endif

      @if ($family->isApproved() && $sponsors !== null && $canManageFinance)
        <form action="{{ route('dashboard.orphans.sponsorship', $family) }}" method="POST" class="{{ $card }}">
          @csrf
          @method('PATCH')
          <h2 class="{{ $cardTitle }}">{{ __('orphans.sponsorship_title') }}</h2>
          <p class="mb-4 flex items-start gap-2 text-theme-xs text-gray-500 dark:text-gray-400">
            <svg class="mt-0.5 size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
            {{ __('orphans.sponsor_private_hint') }}
          </p>
          @if ($sponsors->isEmpty())
            <p class="mb-4 rounded-lg bg-warning-50 p-3 text-theme-xs text-warning-800 dark:bg-warning-500/10 dark:text-warning-400">
              {{ __('sponsors.none_yet') }}
              <a href="{{ route('dashboard.sponsors.create') }}" class="font-semibold underline">{{ __('sponsors.add') }}</a>
            </p>
          @endif
          @if ($family->isWidowFamily())
            @php
              $familySponsorships = $family->activeSponsorships;
              $motherSponsorship = $familySponsorships->firstWhere('beneficiary_type', \App\Models\Sponsorship::BENEFICIARY_MOTHER);
              $anySponsorship = $motherSponsorship ?? $familySponsorships->first();
            @endphp
            <x-dashboard.orphan-sponsor-fields :label="__('orphans.family_sponsorship')" :status="$family->sponsorship_status" :sponsorship="$anySponsorship"
              :sponsors="$sponsors" :input-class="$inputClass" :with-amount="false">
              <div class="space-y-3 border-t border-gray-200 pt-3 dark:border-gray-700">
                <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ __('sponsors.widow_amounts_hint') }}</p>
                <label class="block">
                  <span class="mb-1 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('sponsors.beneficiary.mother') }} — {{ $family->mother_full_name }}</span>
                  <input type="number" name="mother_amount" value="{{ old('mother_amount', $motherSponsorship?->monthly_amount) }}" min="0" max="100000" step="0.01" dir="ltr" class="{{ $inputClass }}">
                </label>
                @foreach ($family->orphans as $orphan)
                  <label class="block">
                    <span class="mb-1 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ $orphan->full_name }}</span>
                    <input type="number" name="orphans[{{ $orphan->id }}][monthly_amount]" value="{{ old('orphans.'.$orphan->id.'.monthly_amount', $familySponsorships->firstWhere('orphan_id', $orphan->id)?->monthly_amount) }}" min="0" max="100000" step="0.01" dir="ltr" class="{{ $inputClass }}">
                  </label>
                @endforeach
                @error('mother_amount') <p class="text-theme-xs text-error-600">{{ $message }}</p> @enderror
              </div>
            </x-dashboard.orphan-sponsor-fields>
          @else
            <div class="space-y-4">
              @foreach ($family->orphans as $orphan)
                <x-dashboard.orphan-sponsor-fields :label="$orphan->full_name" :status="$orphan->sponsorship_status" :sponsorship="$orphan->activeSponsorship"
                  :sponsors="$sponsors" :prefix="'orphans['.$orphan->id.']'" :input-class="$inputClass"
                  class="rounded-xl border border-gray-200 p-3 dark:border-gray-800" />
              @endforeach
            </div>
          @endif
          <button type="submit" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('dashboard.save_changes') }}</button>
        </form>
      @elseif ($family->isApproved())
        <div class="{{ $card }}">
          <h2 class="{{ $cardTitle }}">{{ __('orphans.sponsorship_title') }}</h2>
          <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ __('sponsors.managed_by_admins') }}</p>
        </div>
      @else
        <div class="{{ $card }}">
          <h2 class="{{ $cardTitle }}">{{ __('orphans.sponsorship_title') }}</h2>
          <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ __('orphans.sponsorship_needs_approval') }}</p>
        </div>
      @endif
    </aside>
  </div>
</div>
@endsection
