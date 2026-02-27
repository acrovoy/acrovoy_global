<?php

namespace App\Domain\Media\Repositories;

use App\Domain\Media\Models\Media;

class MediaRepository
{
    /**
     * Create media record
     */
    public function create(array $data): Media
    {
        return Media::create($data);
    }

    /**
     * Find media by UUID
     */
    public function findByUuid(string $uuid): ?Media
    {
        return Media::where('uuid', $uuid)->first();
    }

    /**
     * Get media by polymorphic relation
     */
    public function getByModel(string $modelType, int $modelId)
    {
        return Media::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Search media by collection
     */
    public function getByCollection(string $collection)
    {
        return Media::where('collection', $collection)
            ->get();
    }

    /**
     * Delete media record (soft delete supported)
     */
    public function delete(Media $media): bool
    {
        return (bool) $media->delete();
    }
}