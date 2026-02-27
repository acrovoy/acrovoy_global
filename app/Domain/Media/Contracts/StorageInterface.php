<?php

namespace App\Domain\Media\Contracts;

use Illuminate\Http\UploadedFile;

interface StorageInterface
{
    /**
     * Upload file to storage
     */
    public function upload(
        UploadedFile $file,
        string $path,
        bool $private = false
    ): string;

    /**
     * Delete file from storage
     */
    public function delete(string $path): bool;

    /**
     * Get public or signed URL
     */
    public function getUrl(
        string $path,
        bool $private = false
    ): string;
}