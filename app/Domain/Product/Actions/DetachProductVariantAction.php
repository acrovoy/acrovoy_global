<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use Exception;

class DetachProductVariantAction
{
    /**
     * Убирает продукт из группы вариантов
     *
     * @param Product $product
     * @return void
     * @throws Exception
     */
    public function execute(Product $product): void
    {
        if (!$product->variant_group_id) {
            throw new Exception('Product does not belong to any variant group');
        }

        // Отсоединяем продукт
        $product->update([
            'variant_group_id' => null
        ]);

        // Проверяем, остались ли другие продукты в группе
        $group = $product->variantGroup;

        if ($group && $group->products()->count() === 0) {
            $group->delete();
        }
    }
}