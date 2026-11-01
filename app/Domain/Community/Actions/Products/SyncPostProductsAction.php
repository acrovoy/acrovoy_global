<?php

namespace App\Domain\Community\Actions\Products;

use App\Domain\Community\Models\Post;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SyncPostProductsAction
{
    public function handle(Post $post, array $productIds): void
    {
        $productIds = array_values(array_unique(array_map('intval', $productIds)));

        if (empty($productIds)) {
            $post->products()->sync([]);
            return;
        }

        DB::transaction(function () use ($post, $productIds) {
            $approvedIds = Product::query()
                ->whereIn('id', $productIds)
                ->where('status', 'approved')
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (count($approvedIds) !== count($productIds)) {
                throw ValidationException::withMessages([
                    'products' => 'One or more selected products are unavailable.',
                ]);
            }

            $syncData = [];

            foreach ($productIds as $index => $productId) {
                $syncData[$productId] = [
                    'sort_order' => $index,
                ];
            }

            $post->products()->sync($syncData);
        });
    }
}