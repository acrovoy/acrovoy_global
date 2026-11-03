<?php

namespace App\Domain\Community\Services;

use App\Domain\Community\Models\Post;
use App\Domain\Community\Models\PostCategory;
use App\Domain\Community\Models\PostTranslation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostService
{
    /**
     * Get published posts for community feed.
     */
    public function getFeed(
    string $locale,
    ?int $categoryId = null,
    int $perPage = 12
): LengthAwarePaginator {
    return Post::query()
        ->where('status', Post::STATUS_PUBLISHED)
        ->whereNotNull('published_at')
        ->whereHas('translations', function (Builder $query) use ($locale) {
            $query->whereIn('locale', array_unique([$locale, 'en']));
        })
        ->when($categoryId, function (Builder $query) use ($categoryId) {
            $query->where('post_category_id', $categoryId);
        })
        ->with([
            'author',
            'category.translations',
            'translations',
            'products' => function ($query) {
                $query->where('products.status', 'approved');
            },
        ])
        ->withCount('publishedComments')
        ->orderByDesc('published_at')
        ->paginate($perPage);
}

    /**
     * Get a published post by translated slug.
     */
    public function getPublishedBySlug(
        string $slug,
        string $locale
    ): Post {
        $translation = PostTranslation::query()
            ->where('slug', $slug)
            ->where('locale', $locale)
            ->whereHas('post', function (Builder $query) {
                $query->where('status', Post::STATUS_PUBLISHED)
                    ->whereNotNull('published_at');
            })
            ->first();

        if (!$translation) {
            throw (new ModelNotFoundException())->setModel(Post::class);
        }

        return $translation->post->load([
            'author',
            'category',
            'translations',
            'products' => function ($query) {
                $query->where('status', 'approved');
            },
            'publishedComments.author',
            'publishedComments.replies.author',
        ]);
    }

    /**
     * Get a post for editing.
     */
    public function getForEditing(
        int $postId,
        int $userId
    ): Post {
        return Post::query()
            ->where('id', $postId)
            ->where('user_id', $userId)
            ->with([
                'category',
                'translations',
                'products',
            ])
            ->firstOrFail();
    }

    /**
     * Get a post preview.
     *
     * Only the author can preview unpublished posts.
     */
    public function getPreview(
        int $postId,
        int $userId
    ): Post {
        return Post::query()
            ->where('id', $postId)
            ->where('user_id', $userId)
            ->whereIn('status', [
                Post::STATUS_DRAFT,
                Post::STATUS_PENDING,
                Post::STATUS_REJECTED,
                Post::STATUS_ARCHIVED,
            ])
            ->with([
    'author',
    'category.translations',
    'translations',
    'media',    
    'products.translations',
    'products.images',
])
            ->firstOrFail();
    }

    /**
     * Search published posts.
     */
    public function search(
        string $locale,
        string $search,
        int $perPage = 12
    ): LengthAwarePaginator {
        return Post::query()
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->whereHas('translations', function (Builder $query) use ($locale, $search) {
                $query->where('locale', $locale)
                    ->where(function (Builder $query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('excerpt', 'like', "%{$search}%")
                            ->orWhere('content', 'like', "%{$search}%");
                    });
            })
            ->with([
                'author',
                'category',
                'translations',
            ])
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    /**
     * Resolve translation with fallback to default locale.
     */
    public function resolveTranslation(
        Post $post,
        string $locale,
        string $defaultLocale = 'en'
    ): ?PostTranslation {
        return $post->translations->firstWhere('locale', $locale)
            ?? $post->translations->firstWhere('locale', $defaultLocale);
    }

    /**
     * Get active categories with published posts count.
     */
    public function getCategories()
{
    return PostCategory::query()
        ->where('is_active', true)
        ->with('translations')
        ->withCount([
            'posts as published_posts_count' => function ($query) {
                $query->where('status', 'published');
            },
        ])
        ->orderBy('sort_order')
        ->get();
}

}