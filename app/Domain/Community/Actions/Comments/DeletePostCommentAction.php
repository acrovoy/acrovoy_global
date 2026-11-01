<?php

namespace App\Domain\Community\Actions\Comments;

use App\Domain\Community\Models\PostComment;
use Illuminate\Support\Facades\DB;

class DeletePostCommentAction
{
    public function execute(PostComment $comment): bool
    {
        return DB::transaction(function () use ($comment) {
            $wasPublished = $comment->status === PostComment::STATUS_PUBLISHED;
            $postId = $comment->post_id;

            $deleted = (bool) $comment->delete();

            if ($deleted && $wasPublished) {
                $post = $comment->post()
                    ->lockForUpdate()
                    ->first();

                if ($post && $post->comments_count > 0) {
                    $post->decrement('comments_count');
                }
            }

            return $deleted;
        });
    }
}