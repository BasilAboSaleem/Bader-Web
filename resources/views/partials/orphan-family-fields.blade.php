{{--
    Orphan sponsorship file fields, shared by the public registration page and the dashboard.
    Expects: $family (OrphanFamily), $documentsRequired (bool), $ui (CSS classes for this context).
    Optional: $lockIdentity (bool), $showDocumentLinks (bool, default true).
--}}
@php
    use App\Models\Orphan;
    use App\Models\OrphanFamily;
    use App\Support\OrphanFamilyInput;

    $lockIdentity = $lockIdentity ?? false;
    $showDocumentLinks = $showDocumentLinks ?? true;
    $selectedType = old('type', $family->type ?? OrphanFamily::TYPE_ORPHAN);
    $orphanRows = array_values(old('orphans', $family->exists
        ? $family->orphans->map(fn (Orphan $orphan): array => [
            'id' => $orphan->id,
            'full_name' => $orphan->full_name,
            'birth_date' => $orphan->birth_date?->format('Y-m-d'),
            'national_id' => $orphan->national_id,
            'education_stage' => $orphan->education_stage,
            'health_status' => $orphan->health_status,
            'health_notes' => $orphan->health_notes,
        ])->all()
        : []));
    $orphanRows = $orphanRows !== [] ? $orphanRows : [[]];
    $maxBytes = OrphanFamilyInput::DOCUMENT_MAX_KB * 1024;
    $value = fn (string $field) => old($field, $family->{$field} instanceof \Carbon\CarbonInterface ? $family->{$field}->format('Y-m-d') : $family->{$field});
    $star = '<span class="text-red-600">*</span>';
@endphp

<div data-orphan-form data-too-large-message="{{ __('orphans.document_too_large') }}" class="space-y-6">
    @include('partials.orphan-family-type-fields', ['selectedType' => $selectedType, 'lockIdentity' => $lockIdentity, 'ui' => $ui, 'star' => $star])

    <fieldset data-show-for-type="{{ OrphanFamily::TYPE_WIDOW_FAMILY }}" class="{{ $ui['section'] }}" @disabled($selectedType !== OrphanFamily::TYPE_WIDOW_FAMILY) @if ($selectedType !== OrphanFamily::TYPE_WIDOW_FAMILY) hidden @endif>
        <legend class="sr-only">{{ __('orphans.section.mother') }}</legend>
        <h2 class="{{ $ui['sectionTitle'] }}">{{ __('orphans.section.mother') }}</h2>
        <p class="{{ $ui['sectionHint'] }}">{{ __('orphans.section.mother_hint') }}</p>
        <div class="{{ $ui['grid'] }}">
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_full_name') }} {!! $star !!}</span>
                <input type="text" name="mother_full_name" value="{{ $value('mother_full_name') }}" required maxlength="255" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_national_id') }} {!! $star !!}</span>
                <input type="text" name="mother_national_id" value="{{ $value('mother_national_id') }}" required inputmode="numeric" pattern="\d{9}" maxlength="9" dir="ltr" class="{{ $ui['input'] }}" @readonly($lockIdentity)>
                @if ($lockIdentity)
                    <span class="{{ $ui['hint'] }}">{{ __('orphan_case_update.national_id_locked') }}</span>
                @endif
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_birth_date') }} {!! $star !!}</span>
                <input type="date" name="mother_birth_date" value="{{ $value('mother_birth_date') }}" required max="{{ now()->subDay()->toDateString() }}" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_passport_number') }}</span>
                <input type="text" name="mother_passport_number" value="{{ $value('mother_passport_number') }}" maxlength="30" dir="ltr" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_residence_area') }} {!! $star !!}</span>
                <select name="mother_residence_area" required class="{{ $ui['input'] }}">
                    <option value="">{{ __('orphans.choose') }}</option>
                    @foreach (config('bader.residence_areas') as $governorate => $areas)
                        <optgroup label="{{ __('orphans.governorate.'.$governorate) }}">
                            @foreach ($areas as $area)
                                <option value="{{ $area }}" @selected($value('mother_residence_area') === $area)>{{ __('orphans.area.'.$area) }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_neighborhood') }} {!! $star !!}</span>
                <input type="text" name="mother_neighborhood" value="{{ $value('mother_neighborhood') }}" required maxlength="255" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_nearest_landmark') }} {!! $star !!}</span>
                <input type="text" name="mother_nearest_landmark" value="{{ $value('mother_nearest_landmark') }}" required maxlength="255" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_birth_country') }} {!! $star !!}</span>
                <input type="text" name="mother_birth_country" value="{{ $value('mother_birth_country') }}" required maxlength="100" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_birth_place') }} {!! $star !!}</span>
                <input type="text" name="mother_birth_place" value="{{ $value('mother_birth_place') }}" required maxlength="255" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_nationality') }} {!! $star !!}</span>
                <input type="text" name="mother_nationality" value="{{ $value('mother_nationality') }}" required maxlength="100" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.mother_phone') }} {!! $star !!}</span>
                <input type="tel" name="mother_phone" value="{{ $value('mother_phone') }}" required maxlength="30" dir="ltr" class="{{ $ui['input'] }}">
            </label>
        </div>
    </fieldset>

    <fieldset data-show-for-type="{{ OrphanFamily::TYPE_ORPHAN }}" class="{{ $ui['section'] }}" @disabled($selectedType !== OrphanFamily::TYPE_ORPHAN) @if ($selectedType !== OrphanFamily::TYPE_ORPHAN) hidden @endif>
        <legend class="sr-only">{{ __('orphans.section.contact') }}</legend>
        <h2 class="{{ $ui['sectionTitle'] }}">{{ __('orphans.section.contact') }}</h2>
        <p class="{{ $ui['sectionHint'] }}">{{ __('orphans.section.contact_hint') }}</p>
        <div class="{{ $ui['grid'] }}">
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.contact_name') }} {!! $star !!}</span>
                <input type="text" name="contact_name" value="{{ $value('contact_name') }}" required maxlength="255" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.contact_relation') }} {!! $star !!}</span>
                <input type="text" name="contact_relation" value="{{ $value('contact_relation') }}" required maxlength="100" placeholder="{{ __('orphans.field.contact_relation_placeholder') }}" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.contact_phone') }} {!! $star !!}</span>
                <input type="tel" name="contact_phone" value="{{ $value('contact_phone') }}" required maxlength="30" dir="ltr" class="{{ $ui['input'] }}">
            </label>
        </div>
    </fieldset>

    <section class="{{ $ui['section'] }}">
        <h2 class="{{ $ui['sectionTitle'] }}">{{ __('orphans.section.father') }}</h2>
        <div class="{{ $ui['grid'] }}">
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.father_full_name') }} {!! $star !!}</span>
                <input type="text" name="father_full_name" value="{{ $value('father_full_name') }}" required maxlength="255" class="{{ $ui['input'] }}">
            </label>
            <label class="block">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.father_death_date') }} {!! $star !!}</span>
                <input type="date" name="father_death_date" value="{{ $value('father_death_date') }}" required max="{{ now()->toDateString() }}" class="{{ $ui['input'] }}">
            </label>
            <label class="block sm:col-span-2">
                <span class="{{ $ui['label'] }}">{{ __('orphans.field.father_death_cause') }} {!! $star !!}</span>
                <input type="text" name="father_death_cause" value="{{ $value('father_death_cause') }}" required maxlength="255" class="{{ $ui['input'] }}">
            </label>
        </div>
    </section>

    <section class="{{ $ui['section'] }}">
        <h2 class="{{ $ui['sectionTitle'] }}">{{ __('orphans.section.orphans') }}</h2>
        <label class="mt-4 block max-w-xs">
            <span class="{{ $ui['label'] }}">{{ __('orphans.field.orphans_count') }} {!! $star !!}</span>
            <input type="number" min="1" max="{{ OrphanFamily::MAX_ORPHANS }}" value="{{ count($orphanRows) }}" data-orphan-count class="{{ $ui['input'] }}">
        </label>
        <div class="mt-5 space-y-4" data-orphan-list>
            @foreach ($orphanRows as $index => $row)
                @include('partials.orphan-family-orphan-row', ['index' => $index, 'number' => $loop->iteration, 'row' => $row])
            @endforeach
        </div>
        <template data-orphan-template>
            @include('partials.orphan-family-orphan-row', ['index' => '__INDEX__', 'number' => '__NUMBER__', 'row' => []])
        </template>
    </section>

    <section class="{{ $ui['section'] }}">
        <h2 class="{{ $ui['sectionTitle'] }}">{{ __('orphans.section.documents') }}</h2>
        <p class="{{ $ui['sectionHint'] }}">{{ __('orphans.section.documents_hint', ['size' => '1']) }}</p>
        <div class="{{ $ui['grid'] }}">
            @foreach (OrphanFamily::DOCUMENTS as $document)
                @php
                    $documentOnFile = $family->exists && $family->hasDocument($document);
                    $documentRequired = $documentsRequired || (! $documentOnFile && $lockIdentity);
                @endphp
                <label class="block">
                    <span class="{{ $ui['label'] }}">{{ __('orphans.document.'.$document) }} @if ($documentRequired) {!! $star !!} @endif</span>
                    <input type="file" name="documents[{{ $document }}]" accept="image/jpeg,image/png,image/webp,application/pdf" data-max-bytes="{{ $maxBytes }}" @required($documentRequired) class="{{ $ui['file'] }}">
                    <span class="{{ $ui['hint'] }}">{{ __('orphans.document.'.$document.'_hint') }}</span>
                    @if ($documentOnFile)
                        @if ($showDocumentLinks)
                            <a href="{{ route('dashboard.orphans.document', [$family, $document]) }}" target="_blank" rel="noopener" class="{{ $ui['link'] }}">{{ __('orphans.view_current_document') }}</a>
                        @else
                            <span class="{{ $ui['hint'] }}">{{ __('orphan_case_update.document_on_file') }}</span>
                        @endif
                    @endif
                </label>
            @endforeach
        </div>
    </section>
</div>
