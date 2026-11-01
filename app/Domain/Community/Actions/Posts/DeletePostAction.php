<?php

namespace App\Domain\Community\Actions\Posts;

use App\Domain\Community\Models\Post;

class DeletePostAction
{
    public function execute(Post $post): bool
    {
        return (bool) $post->delete();
    }
}