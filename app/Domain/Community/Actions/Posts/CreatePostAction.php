<?php

namespace App\Domain\Community\Actions\Posts;

use App\Domain\Community\Actions\Products\SyncPostProductsAction;
use App\Domain\Community\Actions\Translations\SyncPostTranslationsAction;
use App\Domain\Community\Models\Post;
use Illuminate\Support\Facades\DB;

class CreatePostAction
{
    public function __construct(
        protected SyncPostTranslationsAction $syncTranslations,
        protected SyncPostProductsAction $syncProducts
    ) {}

    public function execute(
        int $userId,
        int $categoryId,
        string $slug,
        array $translations,
        ?string $companyType = null,
        ?int $companyId = null,
        array $productIds = []
    ): Post {
        return DB::transaction(function () use (
            $userId,
            $categoryId,
            $slug,
            $translations,
            $companyType,
            $companyId,
            $productIds
        ) {
            $post = Post::create([
                'user_id' => $userId,
                'company_type' => $companyType,
                'company_id' => $companyId,
                'post_category_id' => $categoryId,
                'slug' => $slug,
                'status' => Post::STATUS_DRAFT,
            ]);

            $this->syncTranslations->handle($post, $translations);

            if (!empty($productIds)) {
                $this->syncProducts->handle($post, $productIds);
            }

            return $post->load(['translations', 'products']);
        });
    }
}