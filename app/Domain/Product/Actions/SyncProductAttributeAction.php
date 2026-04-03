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
     * @param array $locales // ['en','uk'] если нужно сохранять переводы
     */
    public function execute(Product $product, array $attributes, array $locales = ['en', 'uk']): void
    {
        DB::transaction(function () use ($product, $attributes, $locales) {

            foreach ($attributes as $attributeId => $value) {

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

                // Вставляем новые переводы
                switch ($attribute->type) {

                    case 'text':
                    case 'number':
                        foreach ($locales as $locale) {
                            ProductAttributeValueTranslation::create([
                                'product_attribute_value_id' => $pav->id,
                                'locale' => $locale,
                                'value' => (string)$value,
                            ]);
                        }
                        break;

                    case 'select':
                        // $value может быть single option_id
                        $pav->options()->create([
                            'attribute_option_id' => $value,
                        ]);

                        foreach ($locales as $locale) {
                            ProductAttributeValueTranslation::create([
                                'product_attribute_value_id' => $pav->id,
                                'locale' => $locale,
                                'value' => $pav->options->map(fn($opt) => $opt->translated_value)->implode(', '),
                            ]);
                        }
                        break;

                    case 'multiselect':
                        // $value = массив выбранных option_id
                        foreach ($value as $optionId) {
                            $pav->options()->create([
                                'attribute_option_id' => $optionId,
                            ]);
                        }
                        // Переводы можно хранить объединённой строкой
                        foreach ($locales as $locale) {
                            $translatedValue = implode(', ', $value);
                            ProductAttributeValueTranslation::create([
                                'product_attribute_value_id' => $pav->id,
                                'locale' => $locale,
                                'value' => $translatedValue,
                            ]);
                        }
                        break;

                    case 'boolean':
                        foreach ($locales as $locale) {
                            ProductAttributeValueTranslation::create([
                                'product_attribute_value_id' => $pav->id,
                                'locale' => $locale,
                                'value' => $value ? '1' : '0',
                            ]);
                        }
                        break;

                    default:
                        foreach ($locales as $locale) {
                            ProductAttributeValueTranslation::create([
                                'product_attribute_value_id' => $pav->id,
                                'locale' => $locale,
                                'value' => (string)$value,
                            ]);
                        }
                        break;
                }
            }
        });
    }
}
