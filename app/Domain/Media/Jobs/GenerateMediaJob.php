<?php

namespace App\Domain\Media\Jobs;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Repositories\MediaRepository;
use App\Domain\Media\Jobs\OptimizeMediaJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 10, 30];

    protected string $mediaUuid;

    public function __construct(string $mediaUuid)
    {
        $this->mediaUuid = $mediaUuid;
    }

    public function handle(MediaRepository $repository): void
{
    \Log::info("Running GenerateMediaJob for {$this->mediaUuid}");
    
    try {

        $media = $repository->findByUuid($this->mediaUuid);

        if (!$media) {
            $this->fail("Media not found: {$this->mediaUuid}");
            return;
        }

        if (in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/webp'])) {

            app('media.processor')->generateVariant($media);

        }

        if (
            str_contains($media->mime_type, 'pdf') ||
            str_contains($media->mime_type, 'msword') ||
            str_contains($media->mime_type, 'vnd.openxmlformats-officedocument')
        ) {
            
            app('media.processor')->generateDocumentPreview($media);

        }

        OptimizeMediaJob::dispatch($media->uuid)->onQueue('media');

    } catch (\Throwable $e) {

        if (isset($media)) {
            $media->processing_status = Media::STATUS_FAILED;
            $media->save();
        }

        $this->fail($e);
    }
}
}