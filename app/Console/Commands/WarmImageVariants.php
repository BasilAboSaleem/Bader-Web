<?php

namespace App\Console\Commands;

use App\Support\ResponsiveImage;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('bader:warm-images {--recent= : Only photos uploaded within this many minutes}')]
#[Description('Generate the missing resized AVIF and WebP copies of every uploaded and bundled photo')]
class WarmImageVariants extends Command
{
    public function handle(): int
    {
        $recentMinutes = $this->option('recent');
        $created = ResponsiveImage::warm($recentMinutes !== null ? now()->subMinutes((int) $recentMinutes)->getTimestamp() : null);

        $this->info("Created {$created} image variants.");

        return self::SUCCESS;
    }
}
