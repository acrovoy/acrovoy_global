<?php

namespace App\Domain\Media\Services;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Contracts\StorageInterface;
use App\Domain\Media\Jobs\ProcessMediaJob;
use App\Domain\Media\Jobs\OptimizeMediaJob;
use App\Domain\Media\Jobs\GenerateThumbnailJob;
use App\Domain\Media\Jobs\VideoTranscodingJob;

class MediaProcessingService
{
    /**
     * Dispatch async pipeline
     */
    public function dispatchPipeline(Media $media): void
{
    ProcessMediaJob::dispatch($media->uuid)
        ->onQueue('media');
}

    public function process(Media $media): void
{
    OptimizeMediaJob::dispatch($media->uuid)
    ->onQueue('media');

    
}

public function optimize(Media $media): void
{
    // optimization pipeline logic
}

public function generateThumbnail(Media $media): void
{
    $storage = app(StorageInterface::class);

    $sourcePath = $media->file_path;

    $file = $storage->get($sourcePath);

    $image = Image::make($file)
        ->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
        });

    $thumbPath = str_replace(
        basename($sourcePath),
        'thumb_' . basename($sourcePath),
        $sourcePath
    );

    $storage->put(
        $thumbPath,
        (string) $image->encode()
    );
}


protected function isVideo(Media $media): bool
{
    return str_starts_with($media->mime_type, 'video');
}

public function transcodeVideo(Media $media): void
{
    if (! $this->isVideo($media)) {
        return;
    }

    // Video transcoding pipeline logic here

    $media->update([
        'processing_status' => 'processed'
    ]);
}



}