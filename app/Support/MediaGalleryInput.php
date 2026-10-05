<?php

namespace App\Support;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Validation and saving for the shared dashboard "Photos & videos" panel.
 */
final class MediaGalleryInput
{
    public const MAX_UPLOADS_PER_SAVE = 20;

    public const MAX_VIDEOS = 12;

    /**
     * @return array<string, list<mixed>>
     */
    public static function rules(): array
    {
        return [
            'gallery_files' => ['nullable', 'array', 'max:'.self::MAX_UPLOADS_PER_SAVE],
            'gallery_files.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'gallery_remove' => ['nullable', 'array'],
            'gallery_remove.*' => ['string'],
            'videos' => ['nullable', 'string', 'max:6000', function (string $attribute, mixed $value, Closure $fail): void {
                $lines = self::videoLines($value);

                if (count($lines) > self::MAX_VIDEOS) {
                    $fail(__('dashboard.media.too_many_videos', ['max' => self::MAX_VIDEOS]));

                    return;
                }

                foreach ($lines as $line) {
                    if (VideoEmbed::fromUrl($line) === null) {
                        $fail(__('dashboard.media.unsupported_video', ['url' => $line]));

                        return;
                    }
                }
            }],
        ];
    }

    /**
     * Remove ticked photos, append new uploads and normalise the video links.
     *
     * @param  list<string>  $currentGallery
     * @return array{gallery: list<string>|null, videos: list<string>|null}
     */
    public static function apply(Request $request, array $currentGallery, string $directory): array
    {
        $removed = array_filter((array) $request->input('gallery_remove', []), 'is_string');
        $gallery = [];

        foreach ($currentGallery as $path) {
            if (! in_array($path, $removed, true)) {
                $gallery[] = $path;

                continue;
            }

            if (str_starts_with($path, 'storage/'.$directory.'/')) {
                Storage::disk('public')->delete(substr($path, strlen('storage/')));
            }
        }

        foreach ((array) $request->file('gallery_files', []) as $file) {
            if ($file instanceof UploadedFile) {
                $gallery[] = 'storage/'.$file->store($directory, 'public');
            }
        }

        $videos = self::videoLines($request->input('videos'));

        return [
            'gallery' => $gallery !== [] ? $gallery : null,
            'videos' => $videos !== [] ? $videos : null,
        ];
    }

    /**
     * @return list<string>
     */
    public static function videoLines(mixed $value): array
    {
        if (! is_string($value)) {
            return [];
        }

        return array_values(array_unique(array_filter(
            array_map('trim', preg_split('/\R/', $value) ?: []),
            fn (string $line): bool => $line !== '',
        )));
    }
}
