@props(['programs'])

<section class="band-tint section-y" aria-labelledby="programs-tabs-title">
    <div class="container-bader">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between" data-reveal>
            <div class="max-w-2xl">
                <p class="kicker">{{ __('home.programs_kicker') }}</p>
                <h2 id="programs-tabs-title" class="section-title mt-3">{{ __('home.programs_title') }}</h2>
                <p class="section-lead mt-3">{{ __('home.programs_intro') }}</p>
            </div>
            <a href="{{ route('programs') }}" class="btn-outline shrink-0">
                {{ __('home.programs_view_all_btn') }}
                <x-bader.icon name="arrow" class="h-4 w-4" />
            </a>
        </div>

        @if ($programs->isNotEmpty())
            <div class="mt-8" data-tabs data-reveal>
                <div class="-mx-4 overflow-x-auto px-4 pb-2 [scrollbar-width:none]">
                    <div class="flex w-max gap-2" role="tablist" aria-label="{{ __('home.programs_kicker') }}">
                        @foreach ($programs as $program)
                            <button type="button"
                                class="chip whitespace-nowrap"
                                role="tab"
                                id="program-tab-{{ $program->key }}"
                                aria-controls="program-panel-{{ $program->key }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                tabindex="{{ $loop->first ? '0' : '-1' }}">
                                {{ $program->title }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @foreach ($programs as $program)
                    @php
                        $fallbackImage = 'images/programs/'.$program->key.'.jpg';
                        $programImage = $program->image ?: (file_exists(public_path($fallbackImage)) ? $fallbackImage : 'images/programs/water.jpg');
                    @endphp
                    <div class="mt-6 overflow-hidden rounded-3xl border border-hairline bg-white shadow-card-md"
                        role="tabpanel"
                        id="program-panel-{{ $program->key }}"
                        aria-labelledby="program-tab-{{ $program->key }}"
                        tabindex="0"
                        @unless ($loop->first) hidden @endunless>
                        <div class="grid lg:grid-cols-2">
                            <div class="relative min-h-64 overflow-hidden bg-paper-3 lg:min-h-[26rem]">
                                <img src="{{ asset($programImage) }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full animate-kenburns object-cover motion-reduce:animate-none" data-replay>
                                @if ($program->badge)
                                    <span class="absolute start-4 top-4 rounded-full bg-gold-500 px-3 py-1 text-xs font-extrabold text-teal-950">{{ $program->badge }}</span>
                                @endif
                            </div>
                            <div class="flex flex-col justify-center p-6 sm:p-10">
                                @if ($program->category)
                                    <p class="animate-fade-up text-xs font-extrabold text-forest-700" data-replay>{{ $program->category }}</p>
                                @endif
                                <h3 class="mt-2 animate-fade-up text-2xl font-extrabold text-ink-900 [animation-delay:80ms] sm:text-3xl" data-replay>{{ $program->title }}</h3>
                                @if ($program->description)
                                    <p class="mt-4 animate-fade-up leading-relaxed text-muted [animation-delay:160ms]" data-replay>{{ $program->description }}</p>
                                @endif
                                @if ($program->highlight)
                                    <p class="mt-5 flex animate-fade-up items-start gap-2 rounded-2xl bg-paper-2 p-4 text-sm font-semibold text-ink-800 [animation-delay:220ms]" data-replay>
                                        <x-bader.icon name="sparkle" class="mt-0.5 h-4 w-4 shrink-0 text-forest-600" />
                                        {{ $program->highlight }}
                                    </p>
                                @endif
                                <p class="mt-5 inline-flex animate-fade-up items-center gap-2 text-sm font-bold text-subtle [animation-delay:260ms]" data-replay>
                                    <x-bader.icon name="grid" class="h-4 w-4" />
                                    {{ trans_choice('region.projects_count', $program->projects_count, ['count' => $program->projects_count]) }}
                                </p>
                                <div class="mt-7 flex animate-fade-up flex-wrap gap-3 [animation-delay:300ms]" data-replay>
                                    <a href="{{ route('donate', ['target_type' => 'program', 'target_id' => $program->id]) }}" class="btn-primary">
                                        <x-bader.icon name="heart" class="h-4 w-4" />
                                        {{ __('home.programs_donate_action') }}
                                    </a>
                                    <a href="{{ route('programs.show', $program->key) }}" class="btn-outline">
                                        {{ __('home.programs_details_action') }}
                                        <x-bader.icon name="arrow" class="h-4 w-4" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
