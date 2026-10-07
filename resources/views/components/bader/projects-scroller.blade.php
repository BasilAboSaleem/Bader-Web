@props(['projects'])

@if ($projects->isNotEmpty())
    <section class="band-base section-y" aria-labelledby="home-projects-title" data-scroller>
        <div class="container-bader">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between" data-reveal>
                <div class="max-w-2xl">
                    <p class="kicker">{{ __('home.campaigns_kicker') }}</p>
                    <h2 id="home-projects-title" class="section-title mt-3">{{ __('home.campaigns_heading') }}</h2>
                    <p class="section-lead mt-3">{{ __('home.campaigns_text') }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <button type="button" class="scroller-arrow" data-scroller-prev aria-label="{{ __('home.hero.previous') }}">
                        <x-bader.icon name="chevron-start" class="h-5 w-5" />
                    </button>
                    <button type="button" class="scroller-arrow" data-scroller-next aria-label="{{ __('home.hero.next') }}">
                        <x-bader.icon name="chevron-end" class="h-5 w-5" />
                    </button>
                    <a href="{{ route('campaigns') }}" class="btn-outline ms-2">
                        {{ __('header.all_campaigns') }}
                        <x-bader.icon name="arrow" class="h-4 w-4" />
                    </a>
                </div>
            </div>

            <div class="-mx-4 mt-8 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-4 pb-6 [scrollbar-width:none] sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" data-scroller-track>
                @foreach ($projects as $project)
                    <x-bader.project-card :campaign="$project" class="w-[85%] shrink-0 snap-start sm:w-[22rem]" />
                @endforeach
            </div>
        </div>
    </section>
@endif
