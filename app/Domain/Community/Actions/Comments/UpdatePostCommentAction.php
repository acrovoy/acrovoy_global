<?php

namespace App\Domain\Community\Actions\Comments;

use App\Domain\Community\Models\PostComment;

class UpdatePostCommentAction
{
    public function execute(
        PostComment $comment,
        string $content
    ): PostComment {
        $comment->update([
            'content' => $content,
        ]);

        return $comment->fresh()->load('author');
    }
}