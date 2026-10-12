@props(['model', 'title' => null, 'visiblePhotos' => 9])

@php
    $photos = $model->galleryPhotos();
    $videos = $model->videoEmbeds();
    $galleryGroup = 'gallery-'.class_basename($model).'-'.$model->getKey();
    $mediaTitle = $title ?? $model->title;
    $posterImage = $model->image ?? ($photos[0] ?? null);
@endphp

@if ($photos !== [] || $videos !== [])
    <div {{ $attributes->class(['space-y-10']) }}>
        @if ($videos !== [])
            <section aria-labelledby="{{ $galleryGroup }}-videos">
                <h2 id="{{ $galleryGroup }}-videos" class="flex items-center gap-2 text-xl font-extrabold text-ink-900" data-reveal>
                    <x-bader.icon name="play" class="h-5 w-5 text-forest-600" />
                    {{ __('media.videos') }}
                    <span class="text-sm font-bold text-subtle">({{ count($videos) }})</span>
                </h2>
                <div class="mt-5 grid gap-5 {{ count($videos) > 1 ? 'md:grid-cols-2' : '' }}">
                    @foreach ($videos as $video)
                        @php $videoFrameClass = $video['is_vertical'] ? 'mx-auto aspect-[9/16] w-full max-w-[22rem]' : 'aspect-video'; @endphp
                        @if ($video['is_embeddable'])
                            <div class="overflow-hidden rounded-3xl bg-teal-950 shadow-card-md {{ $videoFrameClass }}" data-reveal>
                                <iframe
                                    src="{{ $video['embed_url'] }}"
                                    title="{{ __('media.video_title', ['title' => $mediaTitle, 'number' => $loop->iteration]) }}"
                                    class="h-full w-full"
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen
                                ></iframe>
                            </div>
                        @else
                            <a href="{{ $video['url'] }}" target="_blank" rel="noopener" class="group relative flex overflow-hidden rounded-3xl bg-teal-950 shadow-card-md {{ $videoFrameClass }}" data-reveal>
                                @if ($posterImage)
                                    <x-bader.responsive-image :src="$posterImage" sizes="(min-width: 768px) 50vw, 100vw" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover opacity-60 transition duration-700 ease-bader group-hover:scale-105" />
                                @endif
                                <span class="absolute inset-0 bg-gradient-to-t from-teal-950 via-teal-950/60 to-teal-950/20" aria-hidden="true"></span>
                                <span class="relative z-10 m-auto flex flex-col items-center gap-4 p-6 text-center text-white">
                                    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-white/95 text-forest-700 shadow-card-md transition duration-300 group-hover:scale-110">
                                        <x-bader.icon name="play" class="h-7 w-7" />
                                    </span>
                                    <span class="max-w-xs text-sm font-semibold text-white/85">{{ __('media.facebook_only') }}</span>
                                    <span class="btn-primary">
                                        <x-bader.icon name="facebook" class="h-4 w-4" />
                                        {{ __('media.watch_on_facebook') }}
                                    </span>
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        @if ($photos !== [])
            <section aria-labelledby="{{ $galleryGroup }}-photos" data-gallery>
                <h2 id="{{ $galleryGroup }}-photos" class="flex items-center gap-2 text-xl font-extrabold text-ink-900" data-reveal>
                    <x-bader.icon name="image" class="h-5 w-5 text-forest-600" />
                    {{ __('media.photos') }}
                    <span class="text-sm font-bold text-subtle">({{ count($photos) }})</span>
                </h2>
                <ul class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($photos as $photo)
                        <li @if ($loop->index >= $visiblePhotos) hidden data-gallery-extra @endif>
                            <a
                                href="{{ \App\Support\ResponsiveImage::url($photo, \App\Support\ResponsiveImage::LIGHTBOX_WIDTH) }}"
                                data-lightbox="{{ $galleryGroup }}"
                                data-close-label="{{ __('common.close') }}"
                                data-prev-label="{{ __('media.previous_photo') }}"
                                data-next-label="{{ __('media.next_photo') }}"
                                class="group relative block aspect-[4/3] cursor-zoom-in overflow-hidden rounded-2xl bg-paper-3"
                            >
                                <x-bader.responsive-image :src="$photo" sizes="(min-width: 1024px) 18rem, (min-width: 640px) 33vw, 50vw" :alt="__('media.photo_alt', ['title' => $mediaTitle, 'number' => $loop->iteration])" loading="lazy" class="h-full w-full object-cover transition duration-700 ease-bader group-hover:scale-105" />
                            </a>
                        </li>
                    @endforeach
                </ul>
                @if (count($photos) > $visiblePhotos)
                    <div class="mt-5 text-center">
                        <button type="button" class="btn-outline" data-gallery-more>
                            <x-bader.icon name="grid" class="h-4 w-4" />
                            {{ __('media.show_all_photos', ['count' => count($photos)]) }}
                        </button>
                    </div>
                @endif
            </section>
        @endif
    </div>
@endif
