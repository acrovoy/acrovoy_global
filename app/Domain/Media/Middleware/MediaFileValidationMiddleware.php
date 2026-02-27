<?php

namespace App\Domain\Media\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaFileValidationMiddleware
{
    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'video/mp4',
        'video/webm',
        'application/pdf'
    ];

    private int $maxFileSize = 10240 * 1024; // 10MB (пример)

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasFile('file')) {
            abort(400, 'File is required');
        }

        $file = $request->file('file');

        if ($file->getSize() > $this->maxFileSize) {
            abort(413, 'File is too large');
        }

        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            abort(415, 'Unsupported file type');
        }

        return $next($request);
    }
}