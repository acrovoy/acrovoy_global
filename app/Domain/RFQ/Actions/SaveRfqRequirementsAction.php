<?php

namespace App\Domain\RFQ\Actions;

use App\Models\Attribute;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Models\RfqAttributeValue;
use App\Domain\RFQ\Models\RfqCustomAttribute;

class SaveRfqRequirementsAction
{
    public function execute(
        int $rfqId,
        int $categoryId,
        array $attributes,
        array $customAttributes = []
    ): void {



        $rfq = Rfq::findOrFail($rfqId);

        /*
        |--------------------------------------------------------------------------
        | SAVE CATEGORY
        |--------------------------------------------------------------------------
        */

        if ($rfq->category_id !== $categoryId) {

            $rfq->update([
                'category_id' => $categoryId
            ]);

            /*
            |--------------------------------------------------------------------------
            | RESET OLD ATTRIBUTE VALUES IF CATEGORY CHANGED
            |--------------------------------------------------------------------------
            */

            $rfq->attributeValues()->delete();

            /*
            |--------------------------------------------------------------------------
            | RESET CUSTOM ATTRIBUTES (ADDED)
            |--------------------------------------------------------------------------
            */

            $rfq->customAttributes()->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE ATTRIBUTE VALUES
        |--------------------------------------------------------------------------
        */

        $attributes = $attributes ?? [];

        foreach ($attributes as $attributeId => $value) {

            $attribute = Attribute::find($attributeId);

            if (!$attribute) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | MULTISELECT
            |--------------------------------------------------------------------------
            */

            if (is_array($value)) {

                $record = RfqAttributeValue::updateOrCreate(

                    [
                        'rfq_id' => $rfqId,
                        'attribute_id' => $attributeId,
                    ],

                    []

                );

                $record->options()->sync($value);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | SINGLE VALUE ATTRIBUTES
            |--------------------------------------------------------------------------
            */

            $payload = $this->prepareValue(
                $attribute->type,
                $value
            );

            RfqAttributeValue::updateOrCreate(

                [
                    'rfq_id' => $rfqId,
                    'attribute_id' => $attributeId,
                ],

                $payload

            );
        }

        /*
|--------------------------------------------------------------------------
| CUSTOM ATTRIBUTES
|--------------------------------------------------------------------------
*/

        $incomingAttributeIds = [];

        foreach ($customAttributes as $item) {

            /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
            if (!empty($item['_delete']) && !empty($item['id'])) {

                $attributeId = $item['id'];

                // удалить связь RFQ ↔ value
                RfqAttributeValue::where('rfq_id', $rfqId)
                    ->where('attribute_id', $attributeId)
                    ->delete();

                // удалить сам custom attribute
                Attribute::where('id', $attributeId)
                    ->where('context', 'rfq')
                    ->where('is_custom', 1)
                    ->delete();

                continue;
            }

            $key  = $item['key'] ?? null;
            $type = $item['type'] ?? 'text';

            if (!$key) {
                continue;
            }

            /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE (RFQ SCOPED)
    |--------------------------------------------------------------------------
    */

            $attribute = Attribute::updateOrCreate(
                [
                    'code' => $key,
                    'context' => 'rfq',
                ],
                [
                    'type' => $type,
                    'is_custom' => 1,
                    'is_system' => 0,
                ]
            );

            /*
    |--------------------------------------------------------------------------
    | VALUE NORMALIZATION
    |--------------------------------------------------------------------------
    */

            $value = $item['value'] ?? null;

            if (is_array($value)) {
                $value = json_encode(array_values($value));
            }

            RfqAttributeValue::updateOrCreate(
                [
                    'rfq_id' => $rfqId,
                    'attribute_id' => $attribute->id,
                ],
                [
                    'value_text' => $type === 'text' ? $value : null,
                    'value_number' => $type === 'number' ? $value : null,
                ]
            );

            /*
    |--------------------------------------------------------------------------
    | OPTIONS (SAFE)
    |--------------------------------------------------------------------------
    */

            if (in_array($type, ['select', 'multiselect'])) {

                // лучше soft replace, а не delete (если потом понадобится история)
                $attribute->options()->delete();

                foreach (($item['options'] ?? []) as $opt) {

                    if (!$opt) continue;

                    $attribute->options()->create([
                        'code' => \Str::slug($opt),
                    ])->translations()->create([
                        'locale' => app()->getLocale(),
                        'value' => $opt,
                    ]);
                }
            }

            $incomingAttributeIds[] = $attribute->id;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALUE MAPPER
    |--------------------------------------------------------------------------
    */

    private function prepareValue(
        string $type,
        mixed $value
    ): array {

        return match ($type) {

            'number',
            'decimal' => [
                'value_number' => $value
            ],

            'boolean' => [
                'value_boolean' => (bool) $value
            ],

            'date' => [
                'value_date' => $value
            ],

            'select' => [
                'attribute_option_id' => $value
            ],

            default => [
                'value_text' => $value
            ],
        };
    }
}
