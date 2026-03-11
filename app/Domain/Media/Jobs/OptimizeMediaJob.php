<?php

namespace App\Domain\Media\Jobs;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Repositories\MediaRepository;
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

    public function handle(MediaRepository $repository): void
    {
        \Log::info("Running OptimizeMediaJob for {$this->mediaUuid}");
        
        try {
            $media = $repository->findByUuid($this->mediaUuid);

            if (!$media) {
                $this->fail("Media not found: {$this->mediaUuid}");
                return;
            }

            $variantsConfig = config("media.collections.{$media->collection}.variants", []);

            // =============================
            // Оптимизация всех variants
            // =============================
            foreach ($variantsConfig as $variant => $width) {
                app('media.processor')->optimizeVariant($media, $variant);
            }

            // =============================
            // Финализируем статус
            // =============================
            $media->processing_status = Media::STATUS_READY;
            $media->save();

        } catch (\Throwable $e) {
            if (isset($media)) {
                $media->processing_status = Media::STATUS_FAILED;
                $media->save();
            }
            $this->fail($e);
        }
    }
}