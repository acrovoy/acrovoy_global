<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Domain\Community\Models\Post;
use App\Domain\Community\Actions\Engagement\TogglePostLikeAction;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    public function __construct(
        protected TogglePostLikeAction $togglePostLikeAction
    ) {
    }

    /**
     * Toggle like on a published post.
     */
    public function toggle(Request $request, int $postId)
    {
        abort_unless(auth()->check(), 401);

        $post = Post::query()
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->findOrFail($postId);

        $liked = $this->togglePostLikeAction->execute(
            post: $post,
            userId: auth()->id()
        );

        $post->refresh();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $post->likes_count,
            ]);
        }

        return back();
    }
}