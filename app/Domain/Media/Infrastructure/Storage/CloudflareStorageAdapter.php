<?php

namespace App\Domain\Media\Infrastructure\Storage;

use App\Domain\Media\Contracts\StorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CloudflareStorageAdapter implements StorageInterface
{
    /**
     * Upload file to Cloudflare-compatible storage
     */
    public function upload(
        UploadedFile $file,
        string $path,
        bool $private = false
    ): string {

        $disk = $private ? 'cloudflare_private' : 'cloudflare_public';

        Storage::disk($disk)->put(
            $path,
            file_get_contents($file->getPathname())
        );

        return $path;
    }

    /**
     * Delete file from storage
     */
    public function delete(string $path): bool
    {
        return Storage::disk('cloudflare_public')->delete($path);
    }

    /**
     * Generate file URL
     */
    public function getUrl(string $path, bool $private = false): string
    {
        $disk = $private ? 'cloudflare_private' : 'cloudflare_public';

        return Storage::disk($disk)->url($path);
    }
}