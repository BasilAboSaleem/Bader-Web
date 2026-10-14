<?php

namespace App\Support;

use GdImage;
use Illuminate\Support\Facades\File;

/**
 * Resized AVIF and WebP copies of local photos, generated on first request into public/variants
 * so the web server serves them directly afterwards. AVIF is about 30% smaller than WebP
 * at the same sharpness; WebP stays as the fallback for browsers without AVIF support.
 */
final class ResponsiveImage
{
    public const WIDTHS = [480, 960, 1280, 1920];

    public const DEFAULT_WIDTH = 960;

    public const LIGHTBOX_WIDTH = 1920;

    /**
     * Encoder quality per output format (0-100).
     *
     * @var array<string, int>
     */
    public const FORMATS = ['avif' => 50, 'webp' => 75];

    private const DIRECTORY = 'variants';

    private const AVIF_SPEED = 6;

    public static function url(?string $image, int $width = self::DEFAULT_WIDTH, string $format = 'webp'): string
    {
        $path = self::localPath($image);

        if ($path === null) {
            return self::original($image);
        }

        return asset(self::DIRECTORY.'/'.$width.'/'.$path.'.'.$format);
    }

    public static function srcset(?string $image, string $format = 'webp'): ?string
    {
        if (self::localPath($image) === null) {
            return null;
        }

        return implode(', ', array_map(
            fn (int $width): string => self::url($image, $width, $format).' '.$width.'w',
            self::WIDTHS,
        ));
    }

    /**
     * Public-relative path of a local raster photo (e.g. "storage/stories/a.jpg"), or null when it cannot be resized.
     */
    public static function localPath(?string $image): ?string
    {
        if (! is_string($image) || trim($image) === '') {
            return null;
        }

        $image = trim($image);
        $siteRoot = rtrim(url('/'), '/').'/';

        if (str_starts_with($image, $siteRoot)) {
            $image = substr($image, strlen($siteRoot));
        } elseif (preg_match('#^([a-z][a-z0-9+.-]*:|//)#i', $image) === 1) {
            return null;
        }

        $path = ltrim(rawurldecode((string) parse_url($image, PHP_URL_PATH)), '/');

        if (str_contains($path, '..') || preg_match('#^(storage|images)/[^\\\\]+\.(jpe?g|png|webp)$#i', $path) !== 1) {
            return null;
        }

        return $path;
    }

    public static function variantPath(int $width, string $path, string $format): string
    {
        return public_path(self::DIRECTORY.'/'.$width.'/'.$path.'.'.$format);
    }

    /**
     * Create the variant file and return its absolute path, or null for unknown widths or formats and missing or unreadable sources.
     */
    public static function generate(int $width, string $path, string $format = 'webp'): ?string
    {
        if (! in_array($width, self::WIDTHS, true) || ! isset(self::FORMATS[$format]) || self::localPath($path) !== $path) {
            return null;
        }

        $source = realpath(public_path($path));

        if ($source === false || ! is_file($source) || ! self::isInsideAllowedRoot($source)) {
            return null;
        }

        $image = @imagecreatefromstring((string) file_get_contents($source));

        if (! $image instanceof GdImage) {
            return null;
        }

        $image = self::orient($image, $source);

        if (imagesx($image) > $width) {
            $resized = imagescale($image, $width, -1, IMG_BICUBIC);

            if ($resized instanceof GdImage) {
                $image = $resized;
            }
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $target = self::variantPath($width, $path, $format);
        $temporary = $target.'.'.bin2hex(random_bytes(4)).'.tmp';

        File::ensureDirectoryExists(dirname($target));

        $encoded = $format === 'avif'
            ? imageavif($image, $temporary, self::FORMATS['avif'], self::AVIF_SPEED)
            : imagewebp($image, $temporary, self::FORMATS['webp']);

        if (! $encoded) {
            File::delete($temporary);

            return null;
        }

        rename($temporary, $target);

        return $target;
    }

    /**
     * Create every missing variant of the uploaded and bundled photos and return how many were created.
     * With a cut-off timestamp, only uploads modified since then are processed.
     */
    public static function warm(?int $uploadedSince = null): int
    {
        $created = 0;

        foreach (self::publicPhotoPaths($uploadedSince) as $path) {
            foreach (array_keys(self::FORMATS) as $format) {
                foreach (self::WIDTHS as $width) {
                    if (! File::exists(self::variantPath($width, $path, $format)) && self::generate($width, $path, $format) !== null) {
                        $created++;
                    }
                }
            }
        }

        return $created;
    }

    /**
     * Public-relative paths such as "storage/stories/a.jpg" and "images/programs/water.jpg".
     *
     * @return list<string>
     */
    private static function publicPhotoPaths(?int $uploadedSince): array
    {
        $paths = [];
        $roots = ['storage' => storage_path('app/public')];

        if ($uploadedSince === null) {
            $roots['images'] = public_path('images');
        }

        foreach ($roots as $prefix => $root) {
            if (! File::isDirectory($root)) {
                continue;
            }

            foreach (File::allFiles($root) as $file) {
                if ($uploadedSince !== null && $file->getMTime() < $uploadedSince) {
                    continue;
                }

                $path = $prefix.'/'.str_replace('\\', '/', $file->getRelativePathname());

                if (self::localPath($path) === $path) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }

    private static function original(?string $image): string
    {
        $image = trim((string) $image);

        if ($image === '' || preg_match('#^([a-z][a-z0-9+.-]*:|//)#i', $image) === 1) {
            return $image;
        }

        return asset($image);
    }

    private static function isInsideAllowedRoot(string $source): bool
    {
        foreach ([public_path(), storage_path('app/public')] as $root) {
            $root = realpath($root);

            if ($root !== false && str_starts_with($source, $root.DIRECTORY_SEPARATOR)) {
                return true;
            }
        }

        return false;
    }

    private static function orient(GdImage $image, string $source): GdImage
    {
        if (! function_exists('exif_read_data') || ! preg_match('/\.jpe?g$/i', $source)) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($source)['Orientation'] ?? 1);
        $angle = match ($orientation) {
            3 => 180,
            6 => 270,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);

        return $rotated instanceof GdImage ? $rotated : $image;
    }
}
