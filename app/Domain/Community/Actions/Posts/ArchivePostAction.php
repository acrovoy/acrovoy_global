<?php

namespace App\Domain\Community\Actions\Posts;

use App\Domain\Community\Models\Post;
use Illuminate\Validation\ValidationException;

class ArchivePostAction
{
    public function execute(Post $post): Post
    {
        if ($post->status !== Post::STATUS_PUBLISHED) {
            throw ValidationException::withMessages([
                'status' => 'Only published posts can be archived.',
            ]);
        }

        $post->update([
            'status' => Post::STATUS_ARCHIVED,
        ]);

        return $post->fresh();
    }
}