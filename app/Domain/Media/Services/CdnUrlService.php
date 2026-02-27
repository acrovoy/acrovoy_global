<?php

namespace App\Domain\Media\Services;

use Illuminate\Support\Str;

class CdnUrlService
{
    /**
     * Generate CDN URL for media file.
     *
     * @param string $path
     * @param bool $private
     * @return string
     */
    public function generate(string $path, bool $private = false): string
    {
        $baseUrl = rtrim(config('media.cdn.base_url'), '/');

        $path = ltrim($path, '/');

        if ($private && config('media.cdn.signed_urls.enabled')) {
            return $this->generateSignedUrl($path);
        }

        return $baseUrl . '/' . $path;
    }

    /**
     * Generate signed CDN URL (future Cloudflare integration ready).
     *
     * Сейчас реализован fallback вариант.
     * Позже можно добавить real signing logic.
     *
     * @param string $path
     * @return string
     */
    protected function generateSignedUrl(string $path): string
    {
        $baseUrl = rtrim(config('media.cdn.base_url'), '/');

        if (!config('media.cdn.signed_urls.enabled')) {
            return $baseUrl . '/' . $path;
        }

        $ttl = config('media.cdn.signed_urls.ttl', 3600);

        $expires = time() + $ttl;

        $token = Str::random(32);

        $query = http_build_query([
            'token' => $token,
            'expires' => $expires
        ]);

        return $baseUrl . '/' . $path . '?' . $query;
    }
}