@extends('layouts.public')

@section('title', __('page.'.$key.'.title').' — '.__('brand.name'))

@php
    use App\Support\SiteSettings;
    use App\Models\ImpactMetric;
@endphp

@section('content')
    <section class="bg-bader-green-deep text-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
            <x-bader.section-heading
                :kicker="__('page.'.$key.'.kicker')"
                :title="__('page.'.$key.'.title')"
                theme="dark"
            />
            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base">{{ SiteSettings::pageIntro($key) }}</p>
        </div>
    </section>

    @if ($key === 'impact')
        @php
            $approvedMetrics = ImpactMetric::approved()->orderBy('order')->get();
        @endphp
        <section class="border-b border-bader-green/10 bg-bader-paper py-14">
            <div class="mx-auto max-w-6xl px-4">
                <x-bader.section-heading :kicker="__('page.impact.kicker')" :title="__('page.impact.metrics_title')" />
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse ($approvedMetrics as $metric)
                        <div class="rounded-2xl border border-bader-green/10 bg-white p-6 text-center shadow-sm" data-reveal>
                            <p class="text-4xl font-bold text-bader-green">{{ $metric->value }}</p>
                            @if ($metric->unit())
                                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-bader-lime">{{ $metric->unit() }}</p>
                            @endif
                            <p class="mt-2 text-sm font-medium text-bader-ink/75">{{ $metric->title() }}</p>
                        </div>
                    @empty
                        <div class="col-span-full rounded-2xl border border-bader-green/10 bg-white p-8 text-center text-sm text-bader-ink/60">
                            {{ __('page.impact.no_metrics_yet') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-6xl px-4 py-14 sm:py-18">
        <div class="grid gap-5 md:grid-cols-3">
            @foreach ($sections as $section)
                <article class="rounded-2xl border border-bader-green/10 bg-white p-6 shadow-[0_1px_0_rgba(16,36,24,0.04)]" data-reveal>
                    <span class="mb-5 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-bader-lime/60 text-bader-green" aria-hidden="true">
                        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-6 w-6">
                    </span>
                    <h2 class="text-xl font-semibold text-bader-green">{{ SiteSettings::institutionalTitle($key, $section) }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-bader-ink/70">{{ SiteSettings::institutionalText($key, $section) }}</p>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Interactive Form Section for Partners, Volunteer, and Sponsorship --}}
    @if (in_array($key, ['partners', 'volunteer', 'sponsorship'], true))
        <section class="border-t border-bader-green/10 bg-bader-paper py-16">
            <div class="mx-auto max-w-3xl px-4">
                <x-bader.section-heading :kicker="__('form.'.$key.'.kicker')" :title="__('form.'.$key.'.heading')" />
                <p class="mt-3 text-sm text-bader-ink/70">{{ __('form.'.$key.'.subheading') }}</p>

                @if (session('success_message'))
                    <div class="mt-6 rounded-2xl border border-bader-green/20 bg-white p-5 text-sm font-semibold text-bader-green-deep">
                        {{ session('success_message') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($key === 'partners')
                    <form method="POST" action="{{ route('partners.submit') }}" class="mt-8 space-y-5" data-reveal>
                        @csrf
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.contact_person') }} *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.organization') }} *</label>
                                <input type="text" name="organization" value="{{ old('organization') }}" required
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.email') }} *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required dir="ltr"
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.phone') }} *</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required dir="ltr"
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.partnership_type') }}</label>
                                <input type="text" name="partnership_type" value="{{ old('partnership_type') }}" placeholder="e.g. تمويل ميداني / رعاية برامج / تزويد عيني"
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.proposal_details') }} *</label>
                            <textarea name="message" rows="4" required class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white p-4 text-sm text-bader-ink focus:border-bader-green focus:outline-none">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="rounded-xl bg-bader-green px-8 py-3.5 text-sm font-semibold text-white hover:bg-bader-green-deep transition">
                            {{ __('form.submit_partnership') }}
                        </button>
                    </form>
                @elseif ($key === 'volunteer')
                    <form method="POST" action="{{ route('volunteer.submit') }}" class="mt-8 space-y-5" data-reveal>
                        @csrf
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.name') }} *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.city_location') }} *</label>
                                <input type="text" name="location" value="{{ old('location') }}" required
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.email') }} *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required dir="ltr"
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.phone') }} *</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required dir="ltr"
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.skills_specialty') }}</label>
                                <input type="text" name="skills" value="{{ old('skills') }}" placeholder="e.g. لوجستيات / تصوير / تمريض / ترجمة"
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.availability') }}</label>
                                <input type="text" name="availability" value="{{ old('availability') }}" placeholder="e.g. عطلات نهاية الأسبوع / دوام كامل"
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.motivation_notes') }}</label>
                            <textarea name="message" rows="3" class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white p-4 text-sm text-bader-ink focus:border-bader-green focus:outline-none">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="rounded-xl bg-bader-green px-8 py-3.5 text-sm font-semibold text-white hover:bg-bader-green-deep transition">
                            {{ __('form.submit_volunteer') }}
                        </button>
                    </form>
                @elseif ($key === 'sponsorship')
                    <form method="POST" action="{{ route('sponsorship.submit') }}" class="mt-8 space-y-5" data-reveal>
                        @csrf
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.sponsor_name') }} *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.sponsorship_type') }} *</label>
                                <select name="sponsorship_type" required class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                                    <option value="orphan">{{ __('form.sponsorship_type_orphan') }}</option>
                                    <option value="widow">{{ __('form.sponsorship_type_widow') }}</option>
                                    <option value="family">{{ __('form.sponsorship_type_family') }}</option>
                                    <option value="student">{{ __('form.sponsorship_type_student') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.email') }} *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required dir="ltr"
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.phone') }} *</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required dir="ltr"
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.beneficiaries_count') }}</label>
                                <input type="number" min="1" max="50" name="beneficiaries_count" value="{{ old('beneficiaries_count', 1) }}"
                                       class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white px-4 py-3 text-sm text-bader-ink focus:border-bader-green focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.preferences_notes') }}</label>
                            <textarea name="message" rows="3" class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-white p-4 text-sm text-bader-ink focus:border-bader-green focus:outline-none">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="rounded-xl bg-bader-green px-8 py-3.5 text-sm font-semibold text-white hover:bg-bader-green-deep transition">
                            {{ __('form.submit_sponsorship') }}
                        </button>
                    </form>
                @endif
            </div>
        </section>
    @endif

    <section class="border-y border-bader-green/10 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap gap-3 px-4 py-10">
            <x-bader.button :href="route('donate')">{{ __('nav.donate') }}</x-bader.button>
            <x-bader.button :href="route('contact')" variant="line">{{ __('nav.contact') }}</x-bader.button>
        </div>
    </section>
@endsection
