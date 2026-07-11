<?php

namespace App\Domain\RFQ\Actions;

use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Models\RfqAttributeValue;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CopyProductAttributesToRfqAction
{
    public function execute(Product $product, Rfq $rfq): void
    {
        $product->load([
            'attributeValues.attribute',
            'attributeValues.translations',
            'attributeValues.options.option.translations',
        ]);

        DB::transaction(function () use ($product, $rfq) {

            foreach ($product->attributeValues as $productValue) {

                $attribute = $productValue->attribute;

                $rfqValue = RfqAttributeValue::create([

                    'rfq_id' => $rfq->id,

                    'attribute_id' => $productValue->attribute_id,

                    'value_text' => $this->textValue($productValue),

                    'value_number' => $this->numberValue($productValue),

                    'value_boolean' => $this->booleanValue($productValue),

                    'value_date' => $this->dateValue($productValue),

                    'attribute_option_id' => $this->singleOption($productValue),

                    'is_source' => true,

                ]);

                /*
                 |------------------------------------------------------------
                 | MULTISELECT
                 |------------------------------------------------------------
                 */

                if ($attribute->type === 'multiselect') {

                    $optionIds = $productValue->options
                        ->pluck('attribute_option_id')
                        ->all();

                    $rfqValue->options()->sync($optionIds);
                }
            }
        });
    }

    protected function textValue($productValue): ?string
    {
        if (!in_array($productValue->attribute->type, [
            'text',
            'textarea',
        ])) {
            return null;
        }

        return $productValue->translations
            ->firstWhere('locale', app()->getLocale())
            ?->value
            ?? $productValue->translations->first()?->value;
    }

    protected function numberValue($productValue): ?float
    {
        if ($productValue->attribute->type !== 'number') {
            return null;
        }

        return (float) (
            $productValue->translations
                ->firstWhere('locale', app()->getLocale())
                ?->value
            ?? $productValue->translations->first()?->value
        );
    }

    protected function booleanValue($productValue): ?bool
    {
        if ($productValue->attribute->type !== 'boolean') {
            return null;
        }

        return filter_var(
            $productValue->translations
                ->firstWhere('locale', app()->getLocale())
                ?->value
            ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
    }

    protected function dateValue($productValue): ?string
    {
        if ($productValue->attribute->type !== 'date') {
            return null;
        }

        return $productValue->translations
            ->firstWhere('locale', app()->getLocale())
            ?->value
            ?? $productValue->translations->first()?->value;
    }

    protected function singleOption($productValue): ?int
    {
        if ($productValue->attribute->type !== 'select') {
            return null;
        }

        return $productValue->options
            ->first()
            ?->attribute_option_id;
    }
}