<?php

namespace App\Domain\Community\Services;

use App\Domain\Community\Models\Post;
use App\Domain\Community\Models\PostComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PostCommentService
{
    /**
     * Get published comments for a post.
     *
     * Only root-level comments are returned.
     * Replies are loaded separately for each comment.
     */
    public function getComments(
        Post $post,
        int $perPage = 10
    ): LengthAwarePaginator {
        return PostComment::query()
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->where('status', PostComment::STATUS_PUBLISHED)
            ->with([
                'author',
                'replies' => function ($query) {
                    $query->where('status', PostComment::STATUS_PUBLISHED)
                        ->with('author')
                        ->orderBy('created_at');
                },
            ])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get all published comments for a post.
     */
    public function getAllComments(Post $post): Collection
    {
        return PostComment::query()
            ->where('post_id', $post->id)
            ->where('status', PostComment::STATUS_PUBLISHED)
            ->with(['author', 'parent'])
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Get a single published comment.
     */
    public function getComment(
        Post $post,
        int $commentId
    ): PostComment {
        return PostComment::query()
            ->where('post_id', $post->id)
            ->where('status', PostComment::STATUS_PUBLISHED)
            ->with(['author', 'replies.author'])
            ->findOrFail($commentId);
    }

    /**
     * Get published replies for a comment.
     */
    public function getReplies(
        PostComment $comment,
        int $perPage = 10
    ): LengthAwarePaginator {
        return PostComment::query()
            ->where('post_id', $comment->post_id)
            ->where('parent_id', $comment->id)
            ->where('status', PostComment::STATUS_PUBLISHED)
            ->with('author')
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    /**
     * Get published comments count.
     */
    public function getCommentsCount(Post $post): int
    {
        return PostComment::query()
            ->where('post_id', $post->id)
            ->where('status', PostComment::STATUS_PUBLISHED)
            ->count();
    }

    /**
     * Get published root-level comments count.
     */
    public function getRootCommentsCount(Post $post): int
    {
        return PostComment::query()
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->where('status', PostComment::STATUS_PUBLISHED)
            ->count();
    }

    /**
     * Check whether a comment belongs to a post.
     */
    public function belongsToPost(
        PostComment $comment,
        Post $post
    ): bool {
        return $comment->post_id === $post->id;
    }
}