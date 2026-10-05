@props(['model'])

@php
  $photos = $model->galleryPhotos();
  $removed = (array) old('gallery_remove', []);
  $videoLines = old('videos', implode("\n", $model->videos ?? []));
@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
  <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
    <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.media') }}</h2>
    <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.section.media_desc') }}</p>
  </div>

  <div class="space-y-6">
    <div>
      <p class="mb-1.5 text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.media.photos') }}</p>

      @if ($photos !== [])
        <p class="mb-3 text-theme-xs text-gray-400">{{ __('dashboard.media.remove_hint') }}</p>
        <ul class="mb-4 grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-6">
          @foreach ($photos as $photo)
            <li>
              <label class="group relative block cursor-pointer overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <img src="{{ asset($photo) }}" alt="" loading="lazy" class="aspect-square w-full object-cover transition group-has-checked:opacity-40">
                <span class="absolute inset-x-0 bottom-0 flex items-center gap-1.5 bg-gray-900/70 px-2 py-1 text-[11px] font-medium text-white group-has-checked:bg-error-600/90">
                  <input type="checkbox" name="gallery_remove[]" value="{{ $photo }}" @checked(in_array($photo, $removed, true)) class="size-3.5 accent-error-500">
                  {{ __('dashboard.media.remove_photo') }}
                </span>
              </label>
            </li>
          @endforeach
        </ul>
      @endif

      <input type="file" id="gallery_files" name="gallery_files[]" accept="image/*" multiple
        class="w-full text-theme-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
      <p class="mt-1 text-theme-xs text-gray-400">{{ __('dashboard.media.upload_hint', ['max' => \App\Support\MediaGalleryInput::MAX_UPLOADS_PER_SAVE]) }}</p>
      @error('gallery_files') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
      @error('gallery_files.*') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
    </div>

    <div>
      <label for="videos" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.media.videos') }}</label>
      <textarea id="videos" name="videos" rows="4" dir="ltr" placeholder="https://www.youtube.com/watch?v=..."
        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 font-mono text-theme-xs text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">{{ $videoLines }}</textarea>
      <p class="mt-1 text-theme-xs text-gray-400">{{ __('dashboard.media.videos_hint', ['max' => \App\Support\MediaGalleryInput::MAX_VIDEOS]) }}</p>
      @error('videos') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
    </div>
  </div>
</div>
