@extends('layouts.dashboard')

@section('title', $family->code . ' — ' . __('dashboard.module.orphans') . ' — ' . __('brand.name'))

@php
  use App\Models\OrphanFamily;

  $card = 'rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6';
  $cardTitle = 'mb-4 border-b border-gray-100 pb-3 text-base font-semibold text-gray-900 dark:border-gray-800 dark:text-white';
  $term = 'text-theme-xs text-gray-500 dark:text-gray-400';
  $detail = 'mt-0.5 text-theme-sm font-medium text-gray-800 dark:text-white/90';
  $inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
  $date = fn ($value) => $value?->format('Y-m-d') ?? '—';
  $reviewBadge = match ($family->review_status) {
    OrphanFamily::REVIEW_APPROVED => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
    OrphanFamily::REVIEW_REJECTED => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
    default => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
  };
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="flex flex-wrap items-center gap-3 text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
        <span dir="ltr">{{ $family->code }}</span>
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $reviewBadge }}">{{ __('orphans.review_status.'.$family->review_status) }}</span>
      </h1>
      <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('dashboard.orphans.index', ['status' => $family->review_status]) }}" class="hover:text-brand-500">{{ __('dashboard.module.orphans') }}</a>
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>{{ __('orphans.type.'.$family->type) }} — {{ __('orphans.source.'.$family->source) }} — <span dir="ltr">{{ $family->created_at->format('Y-m-d H:i') }}</span></span>
      </nav>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('dashboard.orphans.edit', $family) }}"
        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('dashboard.edit') }}</a>
      <form action="{{ route('dashboard.orphans.destroy', $family) }}" method="POST" onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex items-center rounded-lg border border-error-200 bg-error-50 px-4 py-2.5 text-theme-sm font-medium text-error-600 hover:bg-error-100 dark:border-error-500/20 dark:bg-error-500/10 dark:text-error-400">{{ __('dashboard.delete') }}</button>
      </form>
    </div>
  </div>

  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

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

      @if ($family->isWidowFamily())
        <div class="{{ $card }}">
          <h2 class="{{ $cardTitle }}">{{ __('orphans.section.mother') }}</h2>
          <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (['mother_full_name', 'mother_national_id', 'mother_birth_date', 'mother_passport_number', 'mother_residence_area', 'mother_neighborhood', 'mother_nearest_landmark', 'mother_birth_country', 'mother_birth_place', 'mother_nationality', 'mother_phone'] as $field)
              <div>
                <dt class="{{ $term }}">{{ __('orphans.field.'.$field) }}</dt>
                <dd class="{{ $detail }}" @if (in_array($field, ['mother_national_id', 'mother_passport_number', 'mother_phone'], true)) dir="ltr" @endif>
                  @switch($field)
                    @case('mother_birth_date') {{ $date($family->mother_birth_date) }} @break
                    @case('mother_residence_area') {{ $family->mother_residence_area ? __('orphans.area.'.$family->mother_residence_area) : '—' }} @break
                    @default {{ $family->{$field} ?: '—' }}
                  @endswitch
                </dd>
              </div>
            @endforeach
          </dl>
        </div>
      @else
        <div class="{{ $card }}">
          <h2 class="{{ $cardTitle }}">{{ __('orphans.section.contact') }}</h2>
          <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            @foreach (['contact_name', 'contact_relation', 'contact_phone'] as $field)
              <div>
                <dt class="{{ $term }}">{{ __('orphans.field.'.$field) }}</dt>
                <dd class="{{ $detail }}" @if ($field === 'contact_phone') dir="ltr" @endif>{{ $family->{$field} ?: '—' }}</dd>
              </div>
            @endforeach
          </dl>
        </div>
      @endif

      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('orphans.section.father') }}</h2>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <dt class="{{ $term }}">{{ __('orphans.field.father_full_name') }}</dt>
            <dd class="{{ $detail }}">{{ $family->father_full_name }}</dd>
          </div>
          <div>
            <dt class="{{ $term }}">{{ __('orphans.field.father_death_date') }}</dt>
            <dd class="{{ $detail }}" dir="ltr">{{ $date($family->father_death_date) }}</dd>
          </div>
          <div>
            <dt class="{{ $term }}">{{ __('orphans.field.father_death_cause') }}</dt>
            <dd class="{{ $detail }}">{{ $family->father_death_cause }}</dd>
          </div>
        </dl>
      </div>

      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('orphans.section.orphans') }} ({{ $family->orphans->count() }})</h2>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-100 dark:border-gray-800">
                @foreach (['orphan_full_name', 'orphan_birth_date', 'orphan_national_id', 'orphan_education_stage', 'orphan_health_status'] as $field)
                  <th class="px-3 py-2 text-start text-theme-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('orphans.field.'.$field) }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              @foreach ($family->orphans as $orphan)
                <tr>
                  <td class="px-3 py-3 text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $orphan->full_name }}</td>
                  <td class="px-3 py-3 text-theme-sm text-gray-600 dark:text-gray-300"><span dir="ltr">{{ $date($orphan->birth_date) }}</span> ({{ __('sponsorship.age_years', ['age' => $orphan->age]) }})</td>
                  <td class="px-3 py-3 text-theme-sm text-gray-600 dark:text-gray-300" dir="ltr">{{ $orphan->national_id }}</td>
                  <td class="px-3 py-3 text-theme-sm text-gray-600 dark:text-gray-300">{{ __('orphans.education.'.$orphan->education_stage) }}</td>
                  <td class="px-3 py-3 text-theme-sm text-gray-600 dark:text-gray-300">
                    {{ __('orphans.health.'.$orphan->health_status) }}
                    @if ($orphan->health_notes)
                      <span class="block text-theme-xs text-gray-500">{{ $orphan->health_notes }}</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
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

      <form action="{{ route('dashboard.orphans.sponsorship', $family) }}" method="POST" class="{{ $card }}">
        @csrf
        @method('PATCH')
        <h2 class="{{ $cardTitle }}">{{ __('orphans.sponsorship_title') }}</h2>
        @if ($family->isWidowFamily())
          <label for="sponsorship_status" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('orphans.family_sponsorship') }}</label>
          <select id="sponsorship_status" name="sponsorship_status" class="{{ $inputClass }}">
            @foreach (OrphanFamily::SPONSORSHIP_STATUSES as $status)
              <option value="{{ $status }}" @selected($family->sponsorship_status === $status)>{{ __('sponsorship.status.'.$status) }}</option>
            @endforeach
          </select>
        @else
          <input type="hidden" name="sponsorship_status" value="{{ $family->sponsorship_status }}">
          <div class="space-y-3">
            @foreach ($family->orphans as $orphan)
              <label class="block">
                <span class="mb-1 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ $orphan->full_name }}</span>
                <select name="orphans[{{ $orphan->id }}]" class="{{ $inputClass }}">
                  @foreach (OrphanFamily::SPONSORSHIP_STATUSES as $status)
                    <option value="{{ $status }}" @selected($orphan->sponsorship_status === $status)>{{ __('sponsorship.status.'.$status) }}</option>
                  @endforeach
                </select>
              </label>
            @endforeach
          </div>
        @endif
        <button type="submit" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('dashboard.save_changes') }}</button>
      </form>
    </aside>
  </div>
</div>
@endsection
