<?php

namespace App\Domain\Community\Services;

use App\Domain\Community\Models\PostCategory;
use Illuminate\Database\Eloquent\Collection;

class PostCategoryService
{
    /**
     * Get all active categories.
     */
    public function getActiveCategories(): Collection
    {
        return PostCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active categories with published posts count.
     */
    public function getCategoriesWithPublishedPostsCount(): Collection
    {
        return PostCategory::query()
            ->where('is_active', true)
            ->withCount([
                'posts as published_posts_count' => function ($query) {
                    $query
                        ->where('status', 'published')
                        ->whereNotNull('published_at');
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get an active category by slug.
     */
    public function getActiveCategoryBySlug(string $slug): ?PostCategory
    {
        return PostCategory::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get active categories prepared for public filters.
     */
    public function getCategoriesForFilters(): Collection
    {
        return $this->getCategoriesWithPublishedPostsCount()
            ->map(function (PostCategory $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'posts_count' => $category->published_posts_count,
                ];
            });
    }
}