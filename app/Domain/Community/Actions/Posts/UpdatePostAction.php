<?php

namespace App\Domain\Community\Actions\Posts;

use App\Domain\Community\Actions\Products\SyncPostProductsAction;
use App\Domain\Community\Actions\Translations\SyncPostTranslationsAction;
use App\Domain\Community\Models\Post;
use Illuminate\Support\Facades\DB;

class UpdatePostAction
{
    public function __construct(
        protected SyncPostTranslationsAction $syncTranslations,
        protected SyncPostProductsAction $syncProducts
    ) {}

    public function execute(
        Post $post,
        int $categoryId,
        string $slug,
        array $translations,
        array $productIds = []
    ): Post {
        return DB::transaction(function () use (
            $post,
            $categoryId,
            $slug,
            $translations,
            $productIds
        ) {
            $post->update([
                'post_category_id' => $categoryId,
                'slug' => $slug,
            ]);

            $this->syncTranslations->handle($post, $translations);
            $this->syncProducts->handle($post, $productIds);

            return $post->fresh()->load(['translations', 'products']);
        });
    }
}