<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Models\ProductVariantGroup;

class CreateVariantGroupAction
{
    /**
     * Создает новую группу вариантов для продукта
     *
     * @param Product $product
     * @return int Variant Group ID
     */
    public function execute(Product $product): int
    {
        // Если продукт уже в группе, возвращаем существующую
        if ($product->variant_group_id) {
            return $product->variant_group_id;
        }

        $group = ProductVariantGroup::create([
            'name' => $product->name,
            'variant_hash' => uniqid('vg_', true),
        ]);

        $product->update([
            'variant_group_id' => $group->id
        ]);

        return $group->id;
    }
}