<?php

namespace App\Domain\Media\Jobs;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Repositories\MediaRepository;
use App\Domain\Media\Jobs\NormalizeMediaJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class ProcessMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Количество попыток выполнения job
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * Backoff между retry (сек)
     *
     * @var array
     */
    public array $backoff = [5, 10, 30];

    /**
     * UUID медиа
     *
     * @var string
     */
    protected string $mediaUuid;

    /**
     * Create a new job instance.
     *
     * @param string $mediaUuid
     */
    public function __construct(string $mediaUuid)
    {
        $this->mediaUuid = $mediaUuid;
    }

    /**
     * Execute the job.
     *
     * @param MediaRepository $repository
     * @return void
     */
    public function handle(MediaRepository $repository): void
    {
       

        try {

        \Log::info("🟢 ProcessMediaJob START for {$this->mediaUuid}");
            // Получаем медиа по UUID
            $media = $repository->findByUuid($this->mediaUuid);

            if (!$media) {
                $this->fail("Media not found: {$this->mediaUuid}");
                return;
            }

            // Меняем статус на queued
            $media->processing_status = Media::STATUS_QUEUED;
            $media->save();

            // Диспатчим NormalizeMediaJob (первый шаг pipeline)
            NormalizeMediaJob::dispatch($media->uuid)->onQueue('media');

        } catch (\Throwable $e) {

        \Log::error("❌ ProcessMediaJob FAILED: " . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

            if (isset($media)) {
                $media->processing_status = Media::STATUS_FAILED;
                $media->save();
            }
            $this->fail($e);
        }
    }
}