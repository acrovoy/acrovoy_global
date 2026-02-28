<?php

namespace App\Domain\Media\Processing\Resolvers;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Processing\Strategies\Thumbnail\DefaultThumbnailStrategy;

class ThumbnailStrategyResolver
{
    public function resolve(Media $media): object
    {
        return match ($media->collection) {

            'logo',
            'avatar' => new DefaultThumbnailStrategy(),

            default => new DefaultThumbnailStrategy(),
        };
    }
}