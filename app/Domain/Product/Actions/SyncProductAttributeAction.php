<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueTranslation;
use Illuminate\Support\Facades\DB;

class SyncProductAttributeAction
{
    /**
     * Обновляет значения атрибутов продукта
     *
     * @param Product $product
     * @param array $attributes // ['1' => 'Белый', '3' => ['3','5'], ...]
     */
    public function execute(Product $product, array $attributes): void
    {
        DB::transaction(function () use ($product, $attributes) {

            foreach ($attributes as $attributeId => $value) {

                // Пропускаем пустые значения
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    continue;
                }

                $attribute = Attribute::with('options')->find($attributeId);
                if (!$attribute) continue;

                // Получаем или создаём запись product_attribute_values
                $pav = ProductAttributeValue::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'attribute_id' => $attributeId,
                    ],
                    []
                );

                // Удаляем старые переводы
                $pav->translations()->delete();

                // Удаляем старые опции (для мультиселект)
                $pav->options()->delete();

                // Вставляем новые значения/переводы
                switch ($attribute->type) {

                    case 'number':
                    case 'text':
                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => (string)$value,
                        ]);
                        break;

                   

                    case 'select':
                        $pav->options()->create([
                            'attribute_option_id' => $value,
                        ]);
                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => $pav->options->map(fn($opt) => $opt->translated_value)->implode(', '),
                        ]);
                        break;

                    case 'multiselect':
                        foreach ($value as $optionId) {
                            $pav->options()->create([
                                'attribute_option_id' => $optionId,
                            ]);
                        }
                        $translatedValue = implode(', ', $pav->options->map(fn($opt) => $opt->translated_value));
                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => $translatedValue,
                        ]);
                        break;

                    case 'boolean':
                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => $value ? '1' : '0',
                        ]);
                        break;

                    default:
                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => (string)$value,
                        ]);
                        break;
                }
            }
        });
    }
}