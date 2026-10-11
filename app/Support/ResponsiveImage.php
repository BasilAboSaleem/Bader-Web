<?php

namespace App\Support;

use GdImage;
use Illuminate\Support\Facades\File;

/**
 * Resized WebP copies of local photos, generated on first request into public/variants
 * so the web server serves them directly afterwards.
 */
final class ResponsiveImage
{
    public const WIDTHS = [480, 960, 1920];

    public const DEFAULT_WIDTH = 960;

    private const DIRECTORY = 'variants';

    private const QUALITY = 78;

    public static function url(?string $image, int $width = self::DEFAULT_WIDTH): string
    {
        $path = self::localPath($image);

        if ($path === null) {
            return self::original($image);
        }

        return asset(self::DIRECTORY.'/'.$width.'/'.$path.'.webp');
    }

    public static function srcset(?string $image): ?string
    {
        if (self::localPath($image) === null) {
            return null;
        }

        return implode(', ', array_map(
            fn (int $width): string => self::url($image, $width).' '.$width.'w',
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

    /**
     * Create the variant file and return its absolute path, or null for unknown widths and missing or unreadable sources.
     */
    public static function generate(int $width, string $path): ?string
    {
        if (! in_array($width, self::WIDTHS, true) || self::localPath($path) !== $path) {
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

        $target = public_path(self::DIRECTORY.'/'.$width.'/'.$path.'.webp');
        $temporary = $target.'.'.bin2hex(random_bytes(4)).'.tmp';

        File::ensureDirectoryExists(dirname($target));

        if (! imagewebp($image, $temporary, self::QUALITY)) {
            File::delete($temporary);

            return null;
        }

        rename($temporary, $target);

        return $target;
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
