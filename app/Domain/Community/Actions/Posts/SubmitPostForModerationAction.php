<?php

namespace App\Domain\Community\Actions\Posts;

use App\Domain\Community\Models\Post;
use Illuminate\Validation\ValidationException;

class SubmitPostForModerationAction
{
    public function execute(Post $post): Post
    {
        if (!in_array($post->status, [
            Post::STATUS_DRAFT,
            Post::STATUS_REJECTED,
        ], true)) {
            throw ValidationException::withMessages([
                'status' => 'This post cannot be submitted for moderation.',
            ]);
        }

        if (!$post->translations()->exists()) {
            throw ValidationException::withMessages([
                'translations' => 'At least one translation is required.',
            ]);
        }

        $post->update([
            'status' => Post::STATUS_PENDING,
            'reject_reason' => null,
        ]);

        return $post->fresh();
    }
}