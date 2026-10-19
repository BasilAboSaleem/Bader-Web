@extends('layouts.public')

@section('title', __('orphan_case_update.edit_title').' — '.__('brand.name'))
@section('meta_description', __('orphan_case_update.edit_intro'))

@php
    $ui = [
        'section' => 'surface-card min-w-0 p-6 sm:p-8',
        'sectionTitle' => 'text-xl font-extrabold text-ink-900',
        'sectionHint' => 'mt-1 text-sm leading-relaxed text-muted',
        'grid' => 'mt-5 grid gap-4 sm:grid-cols-2',
        'label' => 'field-label',
        'input' => 'field-input',
        'choice' => 'flex cursor-pointer items-center gap-3 rounded-2xl border border-hairline bg-white p-4 font-extrabold text-ink-900 transition has-[:checked]:border-forest-600 has-[:checked]:bg-forest-600/5',
        'item' => 'rounded-2xl border border-hairline bg-paper-2 p-5',
        'itemTitle' => 'font-extrabold text-ink-900',
        'removeButton' => 'text-sm font-bold text-red-700 hover:underline',
        'file' => 'mt-1 block w-full text-sm text-muted file:me-4 file:rounded-full file:border-0 file:bg-forest-600/10 file:px-4 file:py-2 file:font-bold file:text-forest-700',
        'hint' => 'mt-1 block text-xs text-subtle',
        'link' => 'mt-1 inline-block text-xs font-bold text-forest-700 hover:underline',
    ];
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('nav.sponsorship')"
        :title="__('orphan_case_update.edit_title')"
        :intro="__('orphan_case_update.edit_intro')"
        :breadcrumbs="[
            ['label' => __('nav.sponsorship'), 'url' => route('sponsorship')],
            ['label' => __('orphan_case_update.title'), 'url' => route('orphan-case-update')],
        ]"
    />

    <section class="band-base section-y">
        <div class="container-bader max-w-4xl">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex items-start gap-3 rounded-2xl border border-hairline bg-white p-5 text-sm leading-relaxed text-muted sm:flex-1">
                    <x-bader.icon name="lock" class="mt-0.5 h-5 w-5 shrink-0 text-forest-600" />
                    {{ __('orphan_case_update.edit_privacy') }}
                </p>
                <form method="POST" action="{{ route('orphan-case-update.exit') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="btn-ghost w-full sm:w-auto">{{ __('orphan_case_update.exit') }}</button>
                </form>
            </div>

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

            <form method="POST" action="{{ route('orphan-case-update.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                @csrf
                <input type="hidden" name="submission_token" value="{{ $submissionToken }}">
                <div class="sr-only" aria-hidden="true">
                    <label>{{ __('orphan_case_update.edit_title') }}
                        <input type="text" name="{{ \App\Http\Controllers\OrphanCaseUpdateController::HONEYPOT_FIELD }}" value="" tabindex="-1" autocomplete="off">
                    </label>
                </div>
                @include('partials.orphan-family-fields', [
                    'family' => $family,
                    'documentsRequired' => false,
                    'lockIdentity' => true,
                    'showDocumentLinks' => false,
                    'ui' => $ui,
                ])

                <div class="surface-card flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <p class="text-sm leading-relaxed text-muted">{{ __('orphan_case_update.declaration') }}</p>
                    <button type="submit" class="btn-brand shrink-0 disabled:cursor-wait disabled:opacity-70" data-sending-label="{{ __('orphan_case_update.sending') }}">{{ __('orphan_case_update.submit') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
