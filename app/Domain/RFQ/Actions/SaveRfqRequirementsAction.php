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
| CUSTOM ATTRIBUTES (FIXED SYNC LOGIC)
|--------------------------------------------------------------------------
*/


$existing = $rfq->customAttributes()->get()->keyBy('id');

$incomingIds = [];

foreach ($customAttributes as $item) {

    $id = $item['id'] ?? null;

    /*
    |--------------------------------
    | DELETE
    |--------------------------------
    */
    if (!empty($item['_delete']) && $id) {

 

        RfqCustomAttribute::where('id', $id)
            ->where('rfq_id', $rfqId)
            ->delete();

        continue;
    }

    $key = $item['key'] ?? null;
    $val = $item['value'] ?? null;

    if (!$key || $val === null) {
        continue;
    }

    /*
    |--------------------------------
    | UPDATE EXISTING
    |--------------------------------
    */
    if ($id && isset($existing[$id])) {

        $attr = $existing[$id];

        $attr->update([
            'key' => $key,
            'value' => is_array($val) ? json_encode($val) : $val,
            'type' => $item['type'] ?? 'text',
        ]);

        $incomingIds[] = $id;

        continue;
    }

    /*
    |--------------------------------
    | CREATE NEW
    |--------------------------------
    */
    $new = RfqCustomAttribute::create([
        'rfq_id' => $rfqId,
        'key' => $key,
        'value' => is_array($val) ? json_encode($val) : $val,
        'type' => $item['type'] ?? 'text',
    ]);

    $incomingIds[] = $new->id;
}

/*
|--------------------------------
| OPTIONAL CLEANUP SAFETY NET
|--------------------------------
*/
$rfq->customAttributes()
    ->whereNotIn('id', $incomingIds)
    ->delete();
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