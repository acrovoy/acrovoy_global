<?php

namespace App\Domain\Media\Processing\Strategies;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Processing\Contracts\MediaProcessingStrategy;

class LogoProcessingStrategy implements MediaProcessingStrategy
{
    public function process(Media $media): void
    {
        // Example: stricter compression for logo

        $this->optimizeImage($media, 90);
    }

    protected function optimizeImage(Media $media, int $quality): void
    {
        $path = storage_path('app/public/' . $media->file_path);

        if (!file_exists($path)) {
            return;
        }

        $image = imagecreatefromstring(
            file_get_contents($path)
        );

        if ($image === false) {
            return;
        }

        imagewebp($image, $path . '.webp', $quality);

        imagedestroy($image);
    }
}