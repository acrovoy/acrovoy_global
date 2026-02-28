<?php

namespace App\Domain\Media\Processing\Strategies;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Processing\Contracts\MediaProcessingStrategy;
use App\Domain\Media\Contracts\StorageInterface;

class ProductImageProcessingStrategy implements MediaProcessingStrategy
{
    public function __construct(
        protected StorageInterface $storage
    ) {}

    public function process(Media $media): void
{
    $sourcePath = $media->file_path;

    if (! $this->storage->exists($sourcePath)) {
        return;
    }

    $file = $this->storage->get($sourcePath);

    $image = imagecreatefromstring($file);

    if ($image === false) {
        return;
    }

    $basePath = dirname($sourcePath);

    $previewPath = $basePath . '/preview/' . $media->file_name;

    imagewebp($image, $previewPath, 80);

    imagedestroy($image);

    // Save preview through storage adapter
    $this->storage->put(
        $previewPath,
        file_get_contents($previewPath)
    );
}
}