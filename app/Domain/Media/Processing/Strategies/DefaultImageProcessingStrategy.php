<?php

namespace App\Domain\Media\Processing\Strategies;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Processing\Contracts\MediaProcessingStrategy;

class DefaultImageProcessingStrategy implements MediaProcessingStrategy
{
    public function process(Media $media): void
    {
        // Basic optimization pipeline
    }
}