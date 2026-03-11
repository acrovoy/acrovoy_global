<?php

return [

    'storage_driver' => env('MEDIA_STORAGE_DRIVER', 'local'),

    'cdn_url' => env('MEDIA_CDN_URL'),

    // ===============================
    // Стандарты коллекций и variants
    // ===============================
    'collections' => [

        'product_gallery' => [
            'ratio' => '1:1',
            'variants' => [
                'large' => 1400,
                'medium' => 800,
                'small' => 400,
                'thumb' => 150,
            ],
        ],

        'product_thumb' => [
            'ratio' => '1:1',
            'variants' => [
                'thumb' => 120,
            ],
        ],

        'avatars' => [
            'ratio' => '1:1',
            'variants' => [
                'medium' => 200,
                'thumb' => 80,
            ],
        ],

        'company_logos' => [
            'ratio' => '1:1',
            'variants' => [
                'medium' => 300,
                'thumb' => 100,
            ],
        ],

        'catalog_images' => [
            'ratio' => '3:1',
            'variants' => [
                'large' => 1200,
                'medium' => 600,
                'small' => 300,
                'thumb' => 150,
            ],
        ],

        'factory_photos' => [
            'ratio' => '4:3',
            'variants' => [
                'large' => 1200,
                'medium' => 800,
                'small' => 400,
                'thumb' => 150,
            ],
        ],

        'supplier_certificates' => [
            'ratio' => 'A4',
            'variants' => [
                'large' => 1200,
                'medium' => 800,
                'small' => 400,
                'thumb' => 150,
            ],
        ],

        'help_articles' => [
            'ratio' => '16:9',
            'variants' => [
                'large' => 1200,
                'medium' => 800,
                'small' => 400,
                'thumb' => 150,
            ],
        ],

        'help_category_icons' => [
            'ratio' => '1:1',
            'variants' => [
                'thumb' => 100,
            ],
        ],

        'document_previews' => [
            'ratio' => 'A4',
            'variants' => [
                'thumb' => 150,
            ],
        ],

        'product_variant_image' => [
            'ratio' => '1:1',
            'variants' => [
                'large' => 1200,
                'medium' => 800,
                'small' => 400,
                'thumb' => 150,
            ],
        ],

        'product_variant_thumb' => [
            'ratio' => '1:1',
            'variants' => [
                'thumb' => 120,
            ],
        ],

        'help_document_previews' => [
            'ratio' => 'A4',
            'variants' => [
                'thumb' => 150,
            ],
        ],

    ],

];