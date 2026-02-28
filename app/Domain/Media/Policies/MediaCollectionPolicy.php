<?php

namespace App\Domain\Media\Policies;

class MediaCollectionPolicy
{
    public static function allowedMimeTypes(string $collection): array
    {
        return match ($collection) {

            'photos',
            'test_photos',
            'avatar' => [
                'image/jpeg',
                'image/png',
                'image/webp'
            ],

            'certificates' => [
                'image/jpeg',
                'image/png',
                'application/pdf'
            ],

            default => []
        };
    }

    public static function maxFileSize(string $collection): int
{
    return match ($collection) {

        'test_photos' => 1000,
        'photos',
        'avatar' => 10240,

        'certificates' => 20480,

        default => 10240
    };
}

public static function maxWidth(string $collection): ?int
{
    return match ($collection) {

        'avatar' => 1024,
        'photos',
        'test_photos' => 4096,

        default => null
    };
}


public static function maxHeight(string $collection): ?int
{
    return match ($collection) {

        'avatar' => 1024,
        'photos',
        'test_photos' => 4096,

        default => null
    };
}


}