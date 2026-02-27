<?php

namespace App\Domain\Media\Services;

use App\Domain\Media\Contracts\StorageInterface;
use App\Domain\Media\Repositories\MediaRepository;
use App\Domain\Media\Services\CdnUrlService;
use App\Domain\Media\DTO\UploadMediaDTO;

use Illuminate\Support\Str;

class MediaService
{
    protected MediaRepository $repository;

    private StorageInterface $storage;
    private CdnUrlService $cdnService;
    protected MediaProcessingService $processingService;

    public function __construct(
        MediaRepository $repository,
        MediaProcessingService $processingService,
        CdnUrlService $cdnService,
        StorageInterface $storage
        
    ) {
        $this->repository = $repository;
        $this->processingService = $processingService;
        $this->cdnService = $cdnService;
        $this->storage = $storage;
    }

    /**
     * Main upload orchestration entry point
     */
    public function upload(UploadMediaDTO $dto)
    {
        // Override parameters using DTO if provided
        $file = $dto->file;
        $user = $dto->user;
        $collection = $dto->collection;
        $private = $dto->private;

        // 1. Metadata extraction
        $metadata = $this->extractMetadata($file);

        // 2. Generate identity
        $uuid = Str::uuid()->toString();
        

        $fileName = $uuid . '.' . $file->getClientOriginalExtension();
        $path = "media/{$collection}/{$uuid}/{$fileName}";

        // ✅ 1. Upload file FIRST
    $this->storage->upload(
        $file,
        $path,
        $private
    );
        // 3. Persist media record FIRST
        $media = $this->repository->create([
            'uuid' => $uuid,
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'collection' => $collection,
            'file_name' => $fileName,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'size_bytes' => $file->getSize(),
            'width' => $metadata['width'] ?? null,
            'height' => $metadata['height'] ?? null,
            'is_private' => $private,
            'processing_status' => 'pending'
        ]);

        // 4. Dispatch pipeline AFTER media creation
        $this->processingService->dispatchPipeline($media);

        

       

        return $media;
    }

    /**
     * Extract basic metadata
     */
    protected function extractMetadata($file): array
    {
        try {
            if (str_starts_with($file->getMimeType(), 'image')) {

                $imageSize = getimagesize($file->getPathname());

                return [
                    'width' => $imageSize[0] ?? null,
                    'height' => $imageSize[1] ?? null,
                ];
            }
        } catch (\Throwable) {
            // Pipeline should not be broken by metadata extraction
        }

        return [];
    }
}