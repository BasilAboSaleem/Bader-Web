<?php

namespace App\Console\Commands;

use App\Support\ResponsiveImage;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('bader:warm-images')]
#[Description('Generate the missing resized AVIF and WebP copies of every uploaded and bundled photo')]
class WarmImageVariants extends Command
{
    public function handle(): int
    {
        $created = 0;

        foreach ($this->publicPhotoPaths() as $path) {
            foreach (array_keys(ResponsiveImage::FORMATS) as $format) {
                foreach (ResponsiveImage::WIDTHS as $width) {
                    if (! File::exists(ResponsiveImage::variantPath($width, $path, $format))
                        && ResponsiveImage::generate($width, $path, $format) !== null) {
                        $created++;
                    }
                }
            }
        }

        $this->info("Created {$created} image variants.");

        return self::SUCCESS;
    }

    /**
     * Public-relative paths such as "storage/stories/a.jpg" and "images/programs/water.jpg".
     *
     * @return list<string>
     */
    private function publicPhotoPaths(): array
    {
        $paths = [];

        foreach (['storage' => storage_path('app/public'), 'images' => public_path('images')] as $prefix => $root) {
            if (! File::isDirectory($root)) {
                continue;
            }

            foreach (File::allFiles($root) as $file) {
                $path = $prefix.'/'.str_replace('\\', '/', $file->getRelativePathname());

                if (ResponsiveImage::localPath($path) === $path) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }
}
