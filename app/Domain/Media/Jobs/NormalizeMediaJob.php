<?php

namespace App\Domain\Media\Jobs;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Repositories\MediaRepository;
use App\Domain\Media\Jobs\GenerateMediaJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Domain\Media\Services\MediaProcessingService;

class NormalizeMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected MediaProcessingService $processor;

    protected string $mediaUuid;

public function __construct(string $mediaUuid)
{
    $this->mediaUuid = $mediaUuid;
}

public function handle(MediaRepository $repository, MediaProcessingService $processor): void
{
    // $processor приходит автоматически через DI
    \Log::info("🟢 NormalizeMediaJob START for {$this->mediaUuid}");

    $media = $repository->findByUuid($this->mediaUuid);

    if (!$media) {
        \Log::error("❌ Media not found: {$this->mediaUuid}");
        $this->fail("Media not found: {$this->mediaUuid}");
        return;
    }

    $media->processing_status = Media::STATUS_PROCESSING;
    $media->save();

    // нормализация
    if (str_starts_with($media->mime_type, 'image')) {
        $processor->normalize($media);
    }

    // генерация превью для документов
    $documentMimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    if (in_array($media->mime_type, $documentMimes)) {
        $processor->generateDocumentPreview($media);
    }

    // следующий шаг pipeline
    GenerateMediaJob::dispatch($media->uuid)->onQueue('media');
}
}