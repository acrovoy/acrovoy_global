<?php

namespace App\Domain\Media\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaUploadThrottleMiddleware
{
    /**
     * Limit uploads per user/IP.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->user()?->id ?? $request->ip();

        $key = 'media_upload_throttle:' . $userId;

        $maxAttempts = 60; // можно потом tuning делать
        $decayMinutes = 1;

        if (cache()->has($key)) {

            $attempts = cache()->get($key);

            if ($attempts >= $maxAttempts) {
                abort(429, 'Upload rate limit exceeded');
            }

            cache()->increment($key);

        } else {
            cache()->put($key, 1, now()->addMinutes($decayMinutes));
        }

        return $next($request);
    }
}