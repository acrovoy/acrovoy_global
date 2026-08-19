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
     * Обновляет значения атрибутов продукта.
     *
     * Обычные:
     *
     * attributes:
     * [
     *     '1' => 'Белый',
     *     '3' => ['3', '5'],
     *     '10' => '120',
     * ]
     *
     * Measurement:
     *
     * attributes:
     * [
     *     '10' => [
     *         'value' => '120',
     *         'unit_id' => '2',
     *     ],
     * ]
     */
    public function execute(
        Product $product,
        array $attributes
    ): void {

        DB::transaction(function () use (
            $product,
            $attributes
        ) {

            /*
            |--------------------------------------------------------------------------
            | EXISTING MULTISELECT VALUES
            |--------------------------------------------------------------------------
            |
            | Если пользователь снял все checkbox'ы, браузер вообще не отправит
            | этот attribute в request.
            |
            | Поэтому заранее получаем существующие multiselect-значения продукта.
            |
            */

            $existingMultiselectValues = ProductAttributeValue::where(
                'product_id',
                $product->id
            )
                ->whereHas('attribute', function ($query) {
                    $query->where('type', 'multiselect');
                })
                ->get();


            /*
            |--------------------------------------------------------------------------
            | SYNC INCOMING ATTRIBUTES
            |--------------------------------------------------------------------------
            */

            foreach ($attributes as $attributeId => $value) {

                /*
                |--------------------------------------------------------------------------
                | ATTRIBUTE
                |--------------------------------------------------------------------------
                */

                $attribute = Attribute::with('options')
                    ->find($attributeId);

                if (!$attribute) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | MEASUREMENT
                |--------------------------------------------------------------------------
                */

                $unitId = null;

                if ($attribute->type === 'measurement') {

                    $unitId = $value['unit_id']
                        ?? $attribute->unit_id;

                    $value = $value['value']
                        ?? null;
                }


                /*
                |--------------------------------------------------------------------------
                | EMPTY VALUES
                |--------------------------------------------------------------------------
                */

                if (
                    $value === null ||
                    $value === '' ||
                    (
                        $attribute->type === 'multiselect' &&
                        is_array($value) &&
                        empty($value)
                    )
                ) {

                    /*
                    |--------------------------------------------------------------
                    | MULTISELECT EMPTY
                    |--------------------------------------------------------------
                    |
                    | Если multiselect пришёл как пустой массив,
                    | удаляем существующее значение.
                    |
                    */

                    if ($attribute->type === 'multiselect') {

                        $existingValue = ProductAttributeValue::where([
                            'product_id' => $product->id,
                            'attribute_id' => $attributeId,
                        ])->first();

                        if ($existingValue) {

                            $existingValue->options()->delete();
                            $existingValue->translations()->delete();
                            $existingValue->delete();
                        }
                    }

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | PRODUCT ATTRIBUTE VALUE
                |--------------------------------------------------------------------------
                */

                $updateData = [];

                if ($attribute->type === 'measurement') {
                    $updateData['unit_id'] = $unitId;
                }

                $pav = ProductAttributeValue::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'attribute_id' => $attributeId,
                    ],
                    $updateData
                );


                /*
                |--------------------------------------------------------------------------
                | OLD VALUES
                |--------------------------------------------------------------------------
                */

                $pav->translations()->delete();
                $pav->options()->delete();


                /*
                |--------------------------------------------------------------------------
                | SAVE VALUE
                |--------------------------------------------------------------------------
                */

                switch ($attribute->type) {

                    /*
                    |--------------------------------------------------------------------------
                    | NUMBER
                    |--------------------------------------------------------------------------
                    */

                    case 'number':

                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => (string) $value,
                        ]);

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | MEASUREMENT
                    |--------------------------------------------------------------------------
                    */

                    case 'measurement':

                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => (string) $value,
                        ]);

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | TEXT
                    |--------------------------------------------------------------------------
                    */

                    case 'text':

                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => (string) $value,
                        ]);

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | SELECT
                    |--------------------------------------------------------------------------
                    */

                    case 'select':

                        $pav->options()->create([
                            'attribute_option_id' => $value,
                        ]);

                        $options = $pav->options()
                            ->with('option.translations')
                            ->get();

                        $translatedValue = $options
                            ->map(
                                fn ($item) =>
                                    $item->option?->translatedValue('en')
                            )
                            ->filter()
                            ->implode(', ');

                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => $translatedValue,
                        ]);

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | MULTISELECT
                    |--------------------------------------------------------------------------
                    */

                    case 'multiselect':

                        foreach ((array) $value as $optionId) {

                            $pav->options()->create([
                                'attribute_option_id' => $optionId,
                            ]);
                        }

                        $options = $pav->options()
                            ->with('option.translations')
                            ->get();

                        $translatedValue = $options
                            ->map(
                                fn ($item) =>
                                    $item->option?->translatedValue('en')
                            )
                            ->filter()
                            ->implode(', ');

                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => $translatedValue,
                        ]);

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | BOOLEAN
                    |--------------------------------------------------------------------------
                    */

                    case 'boolean':

                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => $value ? '1' : '0',
                        ]);

                        break;


                    /*
                    |--------------------------------------------------------------------------
                    | DEFAULT
                    |--------------------------------------------------------------------------
                    */

                    default:

                        ProductAttributeValueTranslation::create([
                            'product_attribute_value_id' => $pav->id,
                            'locale' => 'en',
                            'value' => (string) $value,
                        ]);

                        break;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | REMOVE CLEARED MULTISELECT ATTRIBUTES
            |--------------------------------------------------------------------------
            |
            | Если checkbox'ы multiselect были сняты ВСЕ,
            | браузер не отправляет attribute вообще.
            |
            | Поэтому удаляем существующие multiselect, которых
            | нет среди входящих attributes.
            |
            */

            foreach ($existingMultiselectValues as $existingValue) {

                $attributeId = (string) $existingValue->attribute_id;

                if (!array_key_exists($attributeId, $attributes)) {

                    $existingValue->options()->delete();
                    $existingValue->translations()->delete();
                    $existingValue->delete();
                }
            }
        });
    }
}