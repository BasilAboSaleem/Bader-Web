<?php

namespace App\Http\Controllers;

use App\Support\ResponsiveImage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageVariantController extends Controller
{
    public function __invoke(int $width, string $path): BinaryFileResponse
    {
        $file = ResponsiveImage::generate($width, Str::beforeLast($path, '.webp'));

        abort_if($file === null, 404);

        return response()->file($file, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
