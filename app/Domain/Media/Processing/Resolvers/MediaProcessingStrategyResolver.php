<?php


namespace App\Domain\Media\Processing\Resolvers;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Processing\Contracts\MediaProcessingStrategy;
use App\Domain\Media\Processing\Strategies\LogoProcessingStrategy;
use App\Domain\Media\Processing\Strategies\DefaultImageProcessingStrategy;
use App\Domain\Media\Processing\Strategies\ProductImageProcessingStrategy;

class MediaProcessingStrategyResolver
{
    public function resolve(Media $media): MediaProcessingStrategy
    {
        return match ($media->collection) {

            'logo',
            'supplier_logo' => app(LogoProcessingStrategy::class),

            'product_images',
            'photos',
            'test_photos' => app(ProductImageProcessingStrategy::class),

            default => app(DefaultImageProcessingStrategy::class),
        };
    }
}