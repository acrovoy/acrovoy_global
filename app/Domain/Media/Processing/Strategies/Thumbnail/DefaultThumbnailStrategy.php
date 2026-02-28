<?php

namespace App\Domain\Media\Processing\Strategies\Thumbnail;

use App\Domain\Media\Models\Media;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DefaultThumbnailStrategy
{
    public function generate(Media $media): void
    {
        $sourcePath = storage_path('app/public/' . $media->file_path);

        if (!file_exists($sourcePath)) {
            return;
        }

        $image = Image::make($sourcePath)
            ->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });

        $thumbPath = str_replace(
            basename($media->file_path),
            'thumb_' . basename($media->file_path),
            $media->file_path
        );

        Storage::disk('public')->put(
            $thumbPath,
            (string) $image->encode()
        );
    }
}