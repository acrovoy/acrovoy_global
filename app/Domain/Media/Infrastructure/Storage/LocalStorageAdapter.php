<?php

namespace App\Domain\Media\Infrastructure\Storage;

use App\Domain\Media\Contracts\StorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalStorageAdapter implements StorageInterface
{
    public function upload(
        UploadedFile $file,
        string $path,
        bool $private = false
    ): string {

        $disk = $private ? 'private' : 'public';

        Storage::disk($disk)->put(
            $path,
            file_get_contents($file->getPathname())
        );

        return $path;
    }

    public function delete(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    public function getUrl(string $path, bool $private = false): string
    {
        $disk = $private ? 'private' : 'public';

        return Storage::disk($disk)->url($path);
    }

    public function exists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }
}