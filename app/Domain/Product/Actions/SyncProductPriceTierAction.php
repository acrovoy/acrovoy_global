<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;

class SyncProductPriceTierAction
{
    public function execute(Product $product, array $tiers): void
    {
        $product->priceTiers()->delete();

        foreach ($tiers as $tier) {

            if (empty($tier['price'])) continue;

            $product->priceTiers()->create([
                'min_qty' => $tier['min_qty'] ?? null,
                'max_qty' => $tier['max_qty'] ?? null,
                'price' => $tier['price']
            ]);
        }
    }
}