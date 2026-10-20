@php
  use App\Support\SponsorshipAmounts;
  $date = fn ($value) => $value?->format('Y-m-d') ?? '—';
  $th = 'border border-gray-200 bg-gray-50 px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-600 dark:border-gray-700 dark:bg-white/[0.04] dark:text-gray-300';
  $td = 'border border-gray-200 px-3 py-2 text-center align-middle text-theme-sm text-gray-800 dark:border-gray-700 dark:text-white/90';
@endphp

<div class="space-y-5">
  @if ($family->isWidowFamily())
    <div>
      <h3 class="mb-2 text-theme-sm font-semibold text-brand-600">1. {{ __('orphans.section.mother') }}</h3>
      <div class="overflow-x-auto">
        <table class="w-full border-collapse">
          <tr>
            <th class="{{ $th }}">{{ __('orphans.field.mother_full_name') }}</th>
            <td class="{{ $td }}">{{ $family->mother_full_name ?: '—' }}</td>
            <th class="{{ $th }}">{{ __('orphans.field.mother_national_id') }}</th>
            <td class="{{ $td }}" dir="ltr">{{ $family->mother_national_id ?: '—' }}</td>
          </tr>
          <tr>
            <th class="{{ $th }}">{{ __('orphans.field.mother_birth_date') }}</th>
            <td class="{{ $td }}" dir="ltr">{{ $date($family->mother_birth_date) }}</td>
            <th class="{{ $th }}">{{ __('orphans.field.mother_passport_number') }}</th>
            <td class="{{ $td }}" dir="ltr">{{ $family->mother_passport_number ?: '—' }}</td>
          </tr>
          <tr>
            <th class="{{ $th }}">{{ __('orphans.field.mother_residence_area') }}</th>
            <td class="{{ $td }}">{{ $family->mother_residence_area ? __('orphans.area.'.$family->mother_residence_area) : '—' }}</td>
            <th class="{{ $th }}">{{ __('orphans.field.mother_birth_country') }}</th>
            <td class="{{ $td }}">{{ $family->mother_birth_country ?: '—' }}</td>
          </tr>
          <tr>
            <th class="{{ $th }}">{{ __('orphans.field.mother_neighborhood') }}</th>
            <td class="{{ $td }}">{{ $family->mother_neighborhood ?: '—' }}</td>
            <th class="{{ $th }}">{{ __('orphans.field.mother_birth_place') }}</th>
            <td class="{{ $td }}">{{ $family->mother_birth_place ?: '—' }}</td>
          </tr>
          <tr>
            <th class="{{ $th }}">{{ __('orphans.field.mother_nearest_landmark') }}</th>
            <td class="{{ $td }}">{{ $family->mother_nearest_landmark ?: '—' }}</td>
            <th class="{{ $th }}">{{ __('orphans.field.mother_nationality') }}</th>
            <td class="{{ $td }}">{{ $family->mother_nationality ?: '—' }}</td>
          </tr>
          <tr>
            <th class="{{ $th }}">{{ __('orphans.field.mother_phone') }}</th>
            <td class="{{ $td }}" dir="ltr" colspan="3">{{ $family->mother_phone ?: '—' }}</td>
          </tr>
        </table>
      </div>
    </div>
  @else
    <div>
      <h3 class="mb-2 text-theme-sm font-semibold text-brand-600">1. {{ __('orphans.section.contact') }}</h3>
      <div class="overflow-x-auto">
        <table class="w-full border-collapse">
          <tr>
            <th class="{{ $th }}">{{ __('orphans.field.contact_name') }}</th>
            <td class="{{ $td }}">{{ $family->contact_name ?: '—' }}</td>
            <th class="{{ $th }}">{{ __('orphans.field.contact_relation') }}</th>
            <td class="{{ $td }}">{{ $family->contact_relation ?: '—' }}</td>
          </tr>
          <tr>
            <th class="{{ $th }}">{{ __('orphans.field.contact_phone') }}</th>
            <td class="{{ $td }}" dir="ltr" colspan="3">{{ $family->contact_phone ?: '—' }}</td>
          </tr>
        </table>
      </div>
    </div>
  @endif

  <div>
    <h3 class="mb-2 text-theme-sm font-semibold text-brand-600">2. {{ __('orphans.section.father') }}</h3>
    <div class="overflow-x-auto">
      <table class="w-full border-collapse">
        <tr>
          <th class="{{ $th }}">{{ __('orphans.field.father_full_name') }}</th>
          <td class="{{ $td }}">{{ $family->father_full_name }}</td>
          <th class="{{ $th }}">{{ __('orphans.field.father_death_date') }}</th>
          <td class="{{ $td }}" dir="ltr">{{ $date($family->father_death_date) }}</td>
        </tr>
        <tr>
          <th class="{{ $th }}">{{ __('orphans.field.father_death_cause') }}</th>
          <td class="{{ $td }}" colspan="3">{{ $family->father_death_cause }}</td>
        </tr>
      </table>
    </div>
  </div>

  <div>
    <h3 class="mb-2 text-theme-sm font-semibold text-brand-600">3. {{ __('orphans.section.orphans') }}</h3>
    <div class="overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr>
            <th class="{{ $th }}">#</th>
            <th class="{{ $th }}">{{ __('orphans.field.orphan_full_name') }}</th>
            <th class="{{ $th }}">{{ __('orphans.field.orphan_birth_date') }}</th>
            <th class="{{ $th }}">{{ __('orphans.field.orphan_national_id') }}</th>
            <th class="{{ $th }}">{{ __('orphans.field.orphan_education_stage') }}</th>
            <th class="{{ $th }}">{{ __('orphans.field.orphan_health_status') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($family->orphans as $index => $orphan)
            <tr>
              <td class="{{ $td }}">{{ $index + 1 }}</td>
              <td class="{{ $td }}">
                <span class="inline-flex max-w-full items-center justify-center gap-2.5 align-middle">
                  @if (($showOrphanPhotos ?? false) && $orphan->photo)
                    <a href="{{ route('dashboard.orphans.photo', [$family, $orphan]) }}" data-lightbox="orphan-portraits" data-close-label="{{ __('common.close') }}" class="shrink-0 cursor-zoom-in">
                      <img src="{{ route('dashboard.orphans.photo', [$family, $orphan]) }}" alt="{{ $orphan->full_name }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700">
                    </a>
                  @endif
                  <span class="min-w-0 text-start leading-5">{{ $orphan->full_name }}</span>
                </span>
              </td>
              <td class="{{ $td }}" dir="ltr">{{ $date($orphan->birth_date) }}</td>
              <td class="{{ $td }}" dir="ltr">{{ $orphan->national_id }}</td>
              <td class="{{ $td }}">{{ __('orphans.education.'.$orphan->education_stage) }}</td>
              <td class="{{ $td }}">
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

  @if ($sponsorship ?? null)
    <div>
      <h3 class="mb-2 text-theme-sm font-semibold text-brand-600">4. {{ __('orphans.sponsorship_title') }}</h3>
      <div class="overflow-x-auto">
        <table class="w-full border-collapse">
          <tr>
            <th class="{{ $th }}">{{ __('sponsors.field.monthly_amount') }}</th>
            <td class="{{ $td }}" dir="ltr">{{ SponsorshipAmounts::format($sponsorship->monthly_amount, $sponsorship->currency) }}</td>
            <th class="{{ $th }}">{{ __('sponsors.field.started_at') }}</th>
            <td class="{{ $td }}" dir="ltr">{{ $date($sponsorship->started_at) }}</td>
          </tr>
          <tr>
            <th class="{{ $th }}">{{ __('sponsors.field.renewal_type') }}</th>
            <td class="{{ $td }}">{{ __('sponsors.renewal.'.$sponsorship->renewal_type) }}</td>
            <th class="{{ $th }}">{{ __('sponsors.field.planned_ended_at') }}</th>
            <td class="{{ $td }}" dir="ltr">{{ $date($sponsorship->planned_ended_at) }}</td>
          </tr>
        </table>
      </div>
    </div>
  @endif
</div>
