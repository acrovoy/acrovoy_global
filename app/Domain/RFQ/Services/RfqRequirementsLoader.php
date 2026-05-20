<?php

namespace App\Domain\Rfq\Services;

use App\Domain\Rfq\Models\Rfq;

class RfqRequirementsLoader
{
    /**
     * Load all requirements-related relations for RFQ workspace
     */
    public function load(Rfq $rfq): Rfq
    {
        $rfq->loadMissing([

            /*
            |--------------------------------------------------------------------------
            | SYSTEM ATTRIBUTES
            |--------------------------------------------------------------------------
            */
            'systemAttributeValues.attribute.translations',
            'systemAttributeValues.attribute.options.translations',
            'systemAttributeValues.options',

            /*
            |--------------------------------------------------------------------------
            | CUSTOM ATTRIBUTES
            |--------------------------------------------------------------------------
            */
            'customAttributeValues.attribute.translations',
            'customAttributeValues.attribute.options.translations',
            'customAttributeValues.options.translations',
        ]);

        return $rfq;
    }
}