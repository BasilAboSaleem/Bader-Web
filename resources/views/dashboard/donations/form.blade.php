@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit_donation') : __('dashboard.record_offline_donation')) . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center gap-3">
    <a href="{{ route('dashboard.donations.index') }}" class="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-400">
      <svg class="size-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
    </a>
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $isEdit ? __('dashboard.edit_donation') : __('dashboard.record_offline_donation') }}</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.donation_form_subtitle') }}</p>
    </div>
  </div>

  {{-- Validation Errors --}}
  @if ($errors->any())
    <div class="rounded-xl border border-error-200 bg-error-50 p-4 text-sm text-error-800 dark:border-error-800/40 dark:bg-error-950/20 dark:text-error-300">
      <ul class="list-inside list-disc space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Form Card --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
    <form method="POST" action="{{ $isEdit ? route('dashboard.donations.update', $donation) : route('dashboard.donations.store') }}" class="space-y-6">
      @csrf
      @if ($isEdit)
        @method('PUT')
      @endif

      <div class="grid gap-6 sm:grid-cols-2">
        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.donor_name') }}</label>
          <input type="text" name="donor_name" value="{{ old('donor_name', $donation->donor_name) }}" placeholder="{{ __('donation.anonymous') }}"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.donor_phone') }}</label>
          <input type="text" name="donor_phone" value="{{ old('donor_phone', $donation->donor_phone) }}" dir="ltr"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.donor_email') }}</label>
          <input type="email" name="donor_email" value="{{ old('donor_email', $donation->donor_email) }}" dir="ltr"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.campaign') }}</label>
          <select name="campaign_id" class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
            <option value="">-- {{ __('dashboard.general_donation') }} --</option>
            @foreach ($campaigns as $camp)
              <option value="{{ $camp->id }}" {{ old('campaign_id', $donation->campaign_id) == $camp->id ? 'selected' : '' }}>
                {{ $camp->title_ar }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.amount') }} *</label>
          <input type="number" step="0.01" name="amount" value="{{ old('amount', $donation->amount) }}" required
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.currency_ar') }}</label>
          <input type="text" name="currency_ar" value="{{ old('currency_ar', $donation->currency_ar ?? 'ريال عماني') }}"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.payment_method') }} *</label>
          <select name="payment_method" class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
            <option value="bank_transfer" {{ old('payment_method', $donation->payment_method) === 'bank_transfer' ? 'selected' : '' }}>{{ __('dashboard.payment_method_bank_transfer') }}</option>
            <option value="cash" {{ old('payment_method', $donation->payment_method) === 'cash' ? 'selected' : '' }}>{{ __('dashboard.payment_method_cash') }}</option>
            <option value="cheque" {{ old('payment_method', $donation->payment_method) === 'cheque' ? 'selected' : '' }}>{{ __('dashboard.payment_method_cheque') }}</option>
            <option value="other" {{ old('payment_method', $donation->payment_method) === 'other' ? 'selected' : '' }}>{{ __('dashboard.payment_method_other') }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.reference_number') }}</label>
          <input type="text" name="reference_number" value="{{ old('reference_number', $donation->reference_number) }}" dir="ltr" placeholder="Bank ref / receipt #"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.transfer_date') }}</label>
          <input type="date" name="transfer_date" value="{{ old('transfer_date', $donation->transfer_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.status') }}</label>
          <select name="status" class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
            <option value="verified" {{ old('status', $donation->status) === 'verified' ? 'selected' : '' }}>{{ __('dashboard.status_verified') }}</option>
            <option value="pending" {{ old('status', $donation->status) === 'pending' ? 'selected' : '' }}>{{ __('dashboard.status_pending') }}</option>
            <option value="rejected" {{ old('status', $donation->status) === 'rejected' ? 'selected' : '' }}>{{ __('dashboard.status_rejected') }}</option>
          </select>
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.internal_notes') }}</label>
          <textarea name="notes" rows="3" class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200" placeholder="{{ __('dashboard.notes_placeholder') }}">{{ old('notes', $donation->notes) }}</textarea>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
        <a href="{{ route('dashboard.donations.index') }}" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
          {{ __('dashboard.cancel') }}
        </a>
        <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-brand-600 transition">
          {{ __('dashboard.save') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
