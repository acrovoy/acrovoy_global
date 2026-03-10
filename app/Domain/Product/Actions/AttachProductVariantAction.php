<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Models\ProductVariantGroup;
use App\Models\ProductVariantItem;
use Illuminate\Support\Str;
use Exception;

class AttachProductVariantAction
{
    /**
     * Присоединяет targetProduct к группе вариаций родительского продукта.
     *
     * @param Product $product       Родительский продукт
     * @param Product $targetProduct Присоединяемый продукт
     * @return ProductVariantGroup
     * @throws Exception
     */
    public function execute(Product $product, Product $targetProduct): ProductVariantGroup
    {
        // Проверяем, что оба продукта одного поставщика
        if ($product->supplier_id !== $targetProduct->supplier_id) {
            throw new Exception('Supplier mismatch');
        }

        // Получаем или создаём группу
        $group = $product->variant_group_id
            ? ProductVariantGroup::findOrFail($product->variant_group_id)
            : ProductVariantGroup::create([
                'name' => $product->name,
                'variant_hash' => Str::uuid()->toString(),
            ]);

        // Обновляем variant_group_id родительского продукта
        if (!$product->variant_group_id) {
            $product->update(['variant_group_id' => $group->id]);
        }

        // Обновляем variant_group_id присоединяемого продукта
        if ($targetProduct->variant_group_id !== $group->id) {
            $targetProduct->update(['variant_group_id' => $group->id]);
        }

        // 🔹 Создаём ProductVariantItem для родителя без media_id
        ProductVariantItem::firstOrCreate(
            [
                'variant_group_id' => $group->id,
                'product_id' => $product->id,
            ],
            [
                'title' => $product->name,
                'media_id' => null, // пока null, медиа присвоим после загрузки
            ]
        );

        // 🔹 Создаём ProductVariantItem для targetProduct без media_id
        ProductVariantItem::firstOrCreate(
            [
                'variant_group_id' => $group->id,
                'product_id' => $targetProduct->id,
            ],
            [
                'title' => $targetProduct->name,
                'media_id' => null, // пока null
            ]
        );

        return $group;
    }
}