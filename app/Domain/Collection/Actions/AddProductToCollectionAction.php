<?php

namespace App\Domain\Collection\Actions;

use App\Domain\Collection\Models\ProductCollection;
use App\Domain\Collection\Models\ProductCollectionable;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AddProductToCollectionAction
{
    /**
     * Add product to collection.
     */
    public function __invoke(
        ProductCollection $collection,
        Product $product,
        ?int $sortOrder = null
    ): ProductCollectionable {

        return DB::transaction(function () use (
            $collection,
            $product,
            $sortOrder
        ) {

            if ($sortOrder === null) {

                $sortOrder = ProductCollectionable::where(
                    'collection_id',
                    $collection->id
                )->max('sort_order') + 1;

            }

            return ProductCollectionable::firstOrCreate(

                [
                    'collection_id' => $collection->id,
                    'collectionable_type' => Product::class,
                    'collectionable_id' => $product->id,
                ],

                [
                    'sort_order' => $sortOrder,
                ]

            );

        });

    }
}