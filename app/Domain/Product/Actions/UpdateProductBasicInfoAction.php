<?php

namespace App\Domain\Product\Actions;

use App\Domain\Product\DTO\ProductBasicInfoDTO;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductBasicInfoAction
{
    public function execute(
        Product $product,
        ProductBasicInfoDTO $data,
        array $translations = [],

    ): Product {

        return DB::transaction(function () use (
            $product,
            $data,
            $translations,

        ) {

            $product->update([
                'slug' => $data->slug,
                'name' => $data->name,
                'sku' => $data->sku,

            ]);

            /* ============================
             * Translations
             * ============================ */

            if (!empty($translations)) {

                foreach ($translations as $locale => $payload) {

                    if (empty($payload['name'])) {
                        continue;
                    }

                    $product->translations()->updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'locale' => $locale
                        ],
                        [
                            'name' => $payload['name'] ?? null,
                            'undername' => $payload['undername'] ?? null,
                            'description' => $payload['description'] ?? null
                        ]
                    );
                }
            }


            // 🔹 Обновляем отношение, чтобы в логах были реальные данные из базы
            $product->load('attributes.options');


            return $product;
        });
    }
}
