<?php

namespace App\Domain\Community\Actions\Engagement;

use App\Domain\Community\Models\Post;
use Illuminate\Support\Facades\Cache;

class IncrementPostViewAction
{
    /**
     * $visitorKey should be a stable visitor identifier,
     * for example an authenticated user ID or a hashed guest identifier.
     */
    public function handle(Post $post, string $visitorKey): bool
    {
        $cacheKey = 'community:post-view:'
            . $post->id . ':'
            . hash('sha256', $visitorKey);

        $counted = Cache::add(
            $cacheKey,
            true,
            now()->addHours(24)
        );

        if (!$counted) {
            return false;
        }

        Post::query()
            ->whereKey($post->id)
            ->increment('views_count');

        return true;
    }
}