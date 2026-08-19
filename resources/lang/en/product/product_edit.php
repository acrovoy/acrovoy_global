<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Category Attributes
    |--------------------------------------------------------------------------
    */

    'category_attributes' => [
        'title' => 'Category Attributes',

        'empty' => [
            'title' => 'No category attributes',
            'description' => 'This category has no attributes configured.',
        ],

        'other_attributes' => [
            'title' => 'Other Attributes',
            'description' => 'Attributes without an assigned group.',
        ],

        'measurements' => [
            'title' => 'Measurements',
            'description' => 'Product dimensions and measurements',
        ],

        'attribute_group_fallback' => 'Attribute Group',
    ],


    /*
    |--------------------------------------------------------------------------
    | Attribute Fields
    |--------------------------------------------------------------------------
    */

    'attributes' => [

        'select_placeholder' => 'Select...',

        'required' => [
            'error' => 'This field is required for this category',
        ],

        'boolean' => [
            'yes' => 'Yes',
        ],
    ],

];