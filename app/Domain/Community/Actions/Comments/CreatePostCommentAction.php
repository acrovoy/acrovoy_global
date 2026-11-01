<?php

namespace App\Domain\Community\Actions\Comments;

use App\Domain\Community\Models\Post;
use App\Domain\Community\Models\PostComment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePostCommentAction
{
    public function execute(
        Post $post,
        int $userId,
        string $content,
        ?int $parentId = null
    ): PostComment {
        return DB::transaction(function () use (
            $post,
            $userId,
            $content,
            $parentId
        ) {
            if ($post->status !== Post::STATUS_PUBLISHED) {
                throw ValidationException::withMessages([
                    'post' => 'Comments are not available for this post.',
                ]);
            }

            if ($parentId !== null) {
                $parent = PostComment::query()
                    ->where('post_id', $post->id)
                    ->where('status', PostComment::STATUS_PUBLISHED)
                    ->find($parentId);

                if (!$parent) {
                    throw ValidationException::withMessages([
                        'parent_id' => 'The parent comment is unavailable.',
                    ]);
                }
            }

            $comment = PostComment::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'parent_id' => $parentId,
                'content' => $content,
                'status' => PostComment::STATUS_PUBLISHED,
            ]);

            Post::query()
                ->whereKey($post->id)
                ->increment('comments_count');

            return $comment->load('author');
        });
    }
}