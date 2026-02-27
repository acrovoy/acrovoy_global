<?php

namespace App\Domain\Media\Jobs;

use App\Domain\Media\Repositories\MediaRepository;
use App\Domain\Media\Services\MediaProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OptimizeMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [5, 10, 30];

    protected string $mediaUuid;

    public function __construct(string $mediaUuid)
    {
        $this->mediaUuid = $mediaUuid;

        
    }

    public function handle(
        MediaProcessingService $processor,
        MediaRepository $repository
    ): void {

        try {

            $media = $repository->findByUuid($this->mediaUuid);

            if (!$media) {
                $this->fail("Media not found: {$this->mediaUuid}");
                return;
            }

            $processor->optimize($media);

        } catch (\Throwable $e) {

            $this->fail($e);
        }
    }
}