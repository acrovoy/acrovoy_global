<?php

namespace App\Domain\Collection\Actions;

use App\Domain\Collection\Models\ProductCollection;
use App\Domain\Collection\Models\ProductCollectionable;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class RemoveProductFromCollectionAction
{
    /**
     * Remove product from collection.
     */
    public function __invoke(
        ProductCollection $collection,
        Product $product
    ): bool {

        return DB::transaction(function () use (
            $collection,
            $product
        ) {

            return ProductCollectionable::query()

                ->where('collection_id', $collection->id)

                ->where('collectionable_type', Product::class)

                ->where('collectionable_id', $product->id)

                ->delete() > 0;

        });

    }
}