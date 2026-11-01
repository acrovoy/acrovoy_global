<?php

namespace App\Domain\Community\Actions\Engagement;

use App\Domain\Community\Models\Post;
use App\Domain\Community\Models\PostLike;
use Illuminate\Support\Facades\DB;

class TogglePostLikeAction
{
    /**
     * Returns true when liked, false when unliked.
     */
    public function execute(Post $post, int $userId): bool
    {
        return DB::transaction(function () use ($post, $userId) {
            $lockedPost = Post::query()
                ->lockForUpdate()
                ->findOrFail($post->id);

            $like = PostLike::query()
                ->where('post_id', $lockedPost->id)
                ->where('user_id', $userId)
                ->first();

            if ($like) {
                $like->delete();

                if ($lockedPost->likes_count > 0) {
                    $lockedPost->decrement('likes_count');
                }

                return false;
            }

            PostLike::create([
                'post_id' => $lockedPost->id,
                'user_id' => $userId,
            ]);

            $lockedPost->increment('likes_count');

            return true;
        });
    }
}