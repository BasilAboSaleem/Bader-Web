@extends('layouts.public')

@section('title', __('orphan_case_update.title').' — '.__('brand.name'))
@section('meta_description', __('orphan_case_update.intro'))

@section('content')
    <x-bader.page-hero
        :kicker="__('nav.sponsorship')"
        :title="__('orphan_case_update.title')"
        :intro="__('orphan_case_update.intro')"
        :breadcrumbs="[['label' => __('nav.sponsorship'), 'url' => route('sponsorship')]]"
    />

    <section class="band-base section-y">
        <div class="container-bader max-w-xl">
            @if ($updated)
                <div class="surface-card p-8 text-center sm:p-12" role="status" data-flash>
                    <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-forest-600/10 text-forest-700">
                        <x-bader.icon name="check" class="h-8 w-8" />
                    </span>
                    <h2 class="mt-5 text-2xl font-extrabold text-ink-900">{{ __('orphan_case_update.success_title') }}</h2>
                    <p class="mx-auto mt-3 max-w-xl leading-8 text-muted">{{ __('orphan_case_update.success_text') }}</p>
                    <a href="{{ route('home') }}" class="btn-brand mt-6">{{ __('orphan_case_update.back_home') }}</a>
                </div>
            @else
                <p class="flex items-start gap-3 rounded-2xl border border-hairline bg-white p-5 text-sm leading-relaxed text-muted">
                    <x-bader.icon name="lock" class="mt-0.5 h-5 w-5 shrink-0 text-forest-600" />
                    {{ __('orphan_case_update.privacy_note') }}
                </p>

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert" data-flash>
                        <p class="font-bold">{{ __('orphan_case_update.fix_errors') }}</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('orphan-case-update.lookup') }}" class="surface-card mt-6 space-y-5 p-6 sm:p-8">
                    @csrf
                    <div class="sr-only" aria-hidden="true">
                        <label>{{ __('orphan_case_update.title') }}
                            <input type="text" name="{{ \App\Http\Controllers\OrphanCaseUpdateController::HONEYPOT_FIELD }}" value="" tabindex="-1" autocomplete="off">
                        </label>
                    </div>
                    <label class="block">
                        <span class="field-label">{{ __('orphan_case_update.national_id') }} <span class="text-red-600">*</span></span>
                        <input
                            type="text"
                            name="national_id"
                            value="{{ old('national_id') }}"
                            required
                            inputmode="numeric"
                            pattern="\d{9}"
                            maxlength="9"
                            autocomplete="off"
                            dir="ltr"
                            class="field-input"
                            placeholder="{{ __('orphan_case_update.national_id_placeholder') }}"
                        >
                        <span class="mt-1 block text-xs text-subtle">{{ __('orphan_case_update.national_id_hint') }}</span>
                    </label>
                    <button type="submit" class="btn-brand w-full sm:w-auto">{{ __('orphan_case_update.lookup_submit') }}</button>
                </form>

                <p class="mt-6 text-center text-sm text-muted">
                    {{ __('orphan_case_update.register_prompt') }}
                    <a href="{{ route('orphan-registration') }}" class="font-bold text-forest-700 hover:underline">{{ __('nav.register_orphan') }}</a>
                </p>
            @endif
        </div>
    </section>
@endsection
