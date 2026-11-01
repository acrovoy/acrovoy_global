<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Domain\Community\Models\Post;
use App\Domain\Community\Models\PostComment;
use App\Domain\Community\Actions\Comments\CreatePostCommentAction;
use App\Domain\Community\Actions\Comments\UpdatePostCommentAction;
use App\Domain\Community\Actions\Comments\DeletePostCommentAction;
use App\Domain\Community\Services\PostCommentService;
use Illuminate\Http\Request;

class PostCommentController extends Controller
{
    public function __construct(
        protected PostCommentService $commentService,
        protected CreatePostCommentAction $createPostCommentAction,
        protected UpdatePostCommentAction $updatePostCommentAction,
        protected DeletePostCommentAction $deletePostCommentAction,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Public comments
    |--------------------------------------------------------------------------
    */

    public function index(Request $request, int $postId)
    {
        $post = $this->getPublishedPost($postId);

        $comments = $this->commentService->getComments(
            post: $post,
            perPage: 10
        );

        return view('community.comments.index', compact(
            'post',
            'comments'
        ));
    }

    public function store(Request $request, int $postId)
    {
        abort_unless(auth()->check(), 401);

        $post = $this->getPublishedPost($postId);

        $validated = $request->validate([
            'content' => [
                'required',
                'string',
                'max:5000',
            ],
            'parent_id' => [
                'nullable',
                'integer',
            ],
        ]);

        $comment = $this->createPostCommentAction->execute(
            post: $post,
            userId: auth()->id(),
            content: $validated['content'],
            parentId: $validated['parent_id'] ?? null,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('community.comment_created'),
                'comment' => $comment,
                'comments_count' => $post->fresh()->comments_count,
            ], 201);
        }

        return back()->with(
            'success',
            __('community.comment_created')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, int $postId, int $commentId)
    {
        abort_unless(auth()->check(), 401);

        $post = $this->getPublishedPost($postId);

        $comment = $this->getManageableComment(
            post: $post,
            commentId: $commentId
        );

        $validated = $request->validate([
            'content' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $comment = $this->updatePostCommentAction->execute(
            comment: $comment,
            content: $validated['content']
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('community.comment_updated'),
                'comment' => $comment,
            ]);
        }

        return back()->with(
            'success',
            __('community.comment_updated')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(Request $request, int $postId, int $commentId)
    {
        abort_unless(auth()->check(), 401);

        $post = $this->getPublishedPost($postId);

        $comment = $this->getManageableComment(
            post: $post,
            commentId: $commentId
        );

        $this->deletePostCommentAction->execute($comment);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('community.comment_deleted'),
                'comments_count' => $post->fresh()->comments_count,
            ]);
        }

        return back()->with(
            'success',
            __('community.comment_deleted')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function getPublishedPost(int $postId): Post
    {
        return Post::query()
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->findOrFail($postId);
    }

    protected function getManageableComment(
    Post $post,
    int $commentId
): PostComment {
    $comment = $this->commentService->getComment(
        post: $post,
        commentId: $commentId
    );

    abort_unless(
        (int) $comment->user_id === (int) auth()->id(),
        403
    );

    return $comment;
}
}