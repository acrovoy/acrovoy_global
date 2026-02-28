<?php

namespace App\Domain\Media\Jobs;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Contracts\StorageInterface;
use App\Domain\Media\Repositories\MediaRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteMediaJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        protected string $mediaUuid
    ) {
        $this->onQueue('media');
    }

    public function handle(
    StorageInterface $storage,
    MediaRepository $repository
): void {


    $media = $repository->findByUuid($this->mediaUuid);
    $baseDirectory = dirname(dirname($media->file_path));

\Log::info('DELETE ROOT DIRECTORY', [
    'directory' => $baseDirectory
]);

$storage->deleteDirectory($baseDirectory);


    $media->forceDelete();
}
}