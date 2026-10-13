@props(['facilities'])

@php
    $count = $facilities->count();
    $gridClass = match ($count) {
        1 => 'lg:grid-cols-1 lg:auto-rows-[26rem]',
        2 => 'lg:grid-cols-2 lg:auto-rows-[24rem]',
        3 => 'lg:grid-cols-3 lg:auto-rows-[16rem]',
        default => 'lg:grid-cols-4 lg:auto-rows-[16rem]',
    };
@endphp

@if ($count > 0)
    <section class="band-base section-y border-t border-hairline" aria-labelledby="facilities-band-title">
        <div class="container-bader">
            <div class="max-w-2xl" data-reveal>
                <p class="kicker">{{ __('home.facilities.kicker') }}</p>
                <h2 id="facilities-band-title" class="section-title mt-3">{{ __('home.facilities.title') }}</h2>
                <p class="section-lead mt-3">{{ __('home.facilities.text') }}</p>
            </div>

            <ul class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 {{ $gridClass }}">
                @foreach ($facilities as $facility)
                    @php
                        $isFeatured = $loop->first && $count >= 3;
                        $spansSmallRow = ($loop->first && $count !== 2) || ($loop->last && $count === 4);
                        $itemClass = collect([
                            $spansSmallRow ? 'sm:col-span-2' : null,
                            $isFeatured ? 'lg:row-span-2' : null,
                            $loop->first && $count === 3 ? 'lg:col-span-2' : null,
                            $count === 4 && $loop->index < 2 ? 'lg:col-span-2' : null,
                            $count === 4 && $loop->last ? 'lg:col-span-1' : null,
                        ])->filter()->implode(' ');
                        $facilityImage = $facility->image ? asset($facility->image) : asset('images/programs/water.jpg');
                    @endphp
                    <li class="{{ $itemClass }}" data-reveal style="animation-delay: {{ $loop->index * 80 }}ms">
                        <a href="{{ route('facilities.show', $facility->key) }}"
                            @class([
                                'group relative flex aspect-[4/3] h-full min-h-60 flex-col justify-end overflow-hidden rounded-3xl bg-teal-950 shadow-card-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-gold-400/60 lg:aspect-auto',
                                'sm:aspect-[16/9]' => $spansSmallRow,
                            ])>
                            <x-bader.responsive-image :src="$facilityImage"
                                :sizes="$isFeatured || $count <= 2 ? '(min-width: 1024px) 50vw, 100vw' : '(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw'"
                                alt="" loading="lazy"
                                class="absolute inset-0 h-full w-full object-cover transition duration-700 ease-bader group-hover:scale-105" />
                            <span class="absolute inset-0 bg-gradient-to-t from-teal-950 via-teal-950/45 to-teal-950/5" aria-hidden="true"></span>

                            <span class="relative flex items-end justify-between gap-4 p-5 sm:p-6">
                                <span class="min-w-0">
                                    @if ($facility->location)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                                            <x-bader.icon name="map-pin" class="h-3.5 w-3.5 text-gold-400" />
                                            {{ $facility->location }}
                                        </span>
                                    @endif
                                    <span @class([
                                        'mt-3 block font-extrabold leading-snug text-white',
                                        'text-2xl sm:text-3xl' => $isFeatured || $count <= 2,
                                        'text-xl' => ! ($isFeatured || $count <= 2),
                                    ])>{{ $facility->name }}</span>
                                    @if ($facility->description)
                                        <span @class([
                                            'mt-2 line-clamp-2 text-sm leading-7 text-white/80',
                                            'lg:hidden' => ! ($isFeatured || $count <= 2),
                                        ])>{{ $facility->description }}</span>
                                    @endif
                                </span>
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-gold-400 text-teal-950 transition group-hover:bg-white">
                                    <x-bader.icon name="arrow" class="h-5 w-5 transition-transform group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5" />
                                    <span class="sr-only">{{ __('facility_page.view') }}</span>
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
