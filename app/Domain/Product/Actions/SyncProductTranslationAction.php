<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Models\ProductTranslation;

class SyncProductTranslationAction
{
    public function execute(Product $product, array $names, array $unders, array $descriptions): void
    {
        foreach ($names as $locale => $name) {

            if (empty($name) &&
                empty($unders[$locale] ?? null) &&
                empty($descriptions[$locale] ?? null)
            ) {
                continue;
            }

            $product->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name' => $name,
                    'undername' => $unders[$locale] ?? null,
                    'description' => $descriptions[$locale] ?? null,
                ]
            );
        }
    }
}