{{-- Sponsorship rows for the team: who is sponsored, in which file, for how much and since when. --}}
@props(['sponsorships', 'showSponsor' => false])

@php
  use App\Support\SponsorshipAmounts;

  $heading = 'px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500 dark:text-gray-400';
  $cell = 'px-3 py-3 text-center align-middle text-theme-sm text-gray-600 dark:text-gray-300';
  $canOpenSponsors = auth()->user()?->canManageFinance() ?? false;
@endphp

<div class="overflow-x-auto">
  <table class="w-full">
    <thead>
      <tr class="border-b border-gray-100 dark:border-gray-800">
        <th class="{{ $heading }}">{{ __('sponsors.column.beneficiary') }}</th>
        @if ($showSponsor)
          <th class="{{ $heading }}">{{ __('sponsors.column.sponsor') }}</th>
        @endif
        <th class="{{ $heading }}">{{ __('sponsors.column.file') }}</th>
        <th class="{{ $heading }}">{{ __('sponsors.column.monthly_amount') }}</th>
        <th class="{{ $heading }}">{{ __('sponsors.column.period') }}</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
      @foreach ($sponsorships as $sponsorship)
        <tr>
          <td class="{{ $cell }} font-medium text-gray-800 dark:text-white/90">
            <span class="block">{{ $sponsorship->beneficiaryName() }}</span>
            <span class="block text-theme-xs font-normal text-gray-500">{{ __('sponsors.beneficiary.'.$sponsorship->beneficiary_type) }}</span>
          </td>
          @if ($showSponsor)
            <td class="{{ $cell }}">
              @if ($canOpenSponsors)
                <a href="{{ route('dashboard.sponsors.show', $sponsorship->sponsor) }}" class="font-medium text-brand-500 hover:underline">{{ $sponsorship->sponsor->name }}</a>
              @else
                {{ $sponsorship->sponsor->name }}
              @endif
            </td>
          @endif
          <td class="{{ $cell }}">
            <a href="{{ route('dashboard.orphans.show', $sponsorship->family) }}" class="font-mono text-theme-xs text-brand-500 hover:underline" dir="ltr">{{ $sponsorship->family->code }}</a>
          </td>
          <td class="{{ $cell }} font-semibold text-gray-800 dark:text-white/90" dir="ltr">{{ SponsorshipAmounts::format($sponsorship->monthly_amount, $sponsorship->currency) }}</td>
          <td class="{{ $cell }} text-theme-xs">
            <span dir="ltr">{{ $sponsorship->started_at->format('Y-m-d') }}</span>
            —
            @if ($sponsorship->ended_at)
              <span dir="ltr">{{ $sponsorship->ended_at->format('Y-m-d') }}</span>
            @elseif ($sponsorship->isPrepaid() && $sponsorship->planned_ended_at)
              <span dir="ltr">{{ $sponsorship->planned_ended_at->format('Y-m-d') }}</span>
              <span class="mt-0.5 block font-medium text-brand-600">{{ __('sponsors.renewal.prepaid') }}</span>
            @else
              <span class="font-medium text-success-600">{{ __('sponsors.ongoing') }}</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
