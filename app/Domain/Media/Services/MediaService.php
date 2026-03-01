<?php

namespace App\Domain\Media\Services;

use App\Domain\Media\Contracts\StorageInterface;
use App\Domain\Media\Repositories\MediaRepository;
use App\Domain\Media\Services\CdnUrlService;
use App\Domain\Media\DTO\UploadMediaDTO;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Jobs\DeleteMediaJob;

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
        $file = $dto->file;
        $model = $dto->model;
        $collection = $dto->collection;
        $private = $dto->private;
        $mediaRole = $dto->mediaRole;
        $originalFileName = $dto->originalFileName;
        

        $metadata = $this->extractMetadata($file);
        $metadata = $dto->metadata;

        $uuid = Str::uuid()->toString();

        $fileName = $uuid . '.' . $file->getClientOriginalExtension();

        $basePath = "media/{$collection}/{$uuid}";
        $originalPath = "{$basePath}/original/{$fileName}";

        $media = null;

        try {

            // State transition → uploading stage
            $media = $this->repository->create([
                'uuid' => $uuid,
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collection,
                'media_role' => $mediaRole,
                'original_file_name' => $originalFileName,
                'file_name' => $fileName,
                'file_path' => $originalPath,
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'size_bytes' => $file->getSize(),
                'width' => $metadata['width'] ?? null,
                'height' => $metadata['height'] ?? null,
                'metadata' => json_encode($metadata),
                'is_private' => $private,
                'processing_status' => 'uploading'
            ]);

            // Upload storage artifact
            $this->storage->upload(
                $file,
                $originalPath,
                $private
            );

            // Update CDN + processing state
            $cdnUrl = $this->cdnService->generate($originalPath, $private);

            $media->update([
                'cdn_url' => $cdnUrl,
                'processing_status' => 'queued'
            ]);

            // Async pipeline dispatch
            $this->processingService->dispatchPipeline($media, 2);

            return $media;

        } catch (\Throwable $e) {

            if ($media) {
                $media->update([
                    'processing_status' => 'failed'
                ]);
            }

            throw $e;
        }
    }

    /**
     * Extract basic metadata
     */
    public function extractMetadata($file): array
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

    public function delete(Media $media): void
{
    $media->update([
        'processing_status' => 'deleting'
    ]);

    DeleteMediaJob::dispatch($media->uuid)
        ->onQueue('media');
}

}