<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueTranslation;
use Illuminate\Support\Facades\DB;

class SyncProductCustomAttributeAction
{
    public function execute(Product $product, array $attributes): void
    {
        foreach ($attributes as $attributeId => $data) {

            $value = $data['value'] ?? null;

            if (is_array($value)) {
                $value = json_encode($value);
            }

            ProductAttributeValue::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'attribute_id' => $attributeId,
                ],
                [
                    'value_text' => is_string($value) ? $value : null,
                ]
            );
        }
    }
}