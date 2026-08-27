<?php

namespace App\Domain\Product\Actions;

use App\Domain\Product\DTO\ProductCategoryDTO;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductCategoryAction
{
    public function __construct(
        private SyncProductAttributeAction $attribute,
    ) {}

    public function execute(
        Product $product,
        ProductCategoryDTO $data,
    ): Product {

        return DB::transaction(function () use (
            $product,
            $data,
        ) {

            $oldCategoryId = $product->category_id;
            $newCategoryId = $data->categoryId;

            /*
            |--------------------------------------------------------------------------
            | UPDATE CATEGORY
            |--------------------------------------------------------------------------
            */

            $product->update([
                'category_id' => $newCategoryId,
            ]);


            /*
            |--------------------------------------------------------------------------
            | CATEGORY CHANGED
            |--------------------------------------------------------------------------
            |
            | Если категория изменилась, старые системные значения
            | атрибутов больше не относятся к продукту.
            |
            */

            if ($oldCategoryId !== $newCategoryId) {

                $oldValues = $product->attributeValues()
                    ->whereHas('attribute', function ($q) {
                        $q->where('is_custom', 0);
                    })
                    ->with('options', 'translations')
                    ->get();

                foreach ($oldValues as $pav) {

                    $pav->options()->delete();
                    $pav->translations()->delete();
                    $pav->delete();
                }

               
            }


            /*
            |--------------------------------------------------------------------------
            | ATTRIBUTE PIPELINE
            |--------------------------------------------------------------------------
            */

            if (!empty($data->attributes)) {

                $this->attribute->execute(
                    $product,
                    $data->attributes
                );

                $product->load('attributes.options');

               
            }


            return $product;
        });
    }
}
