<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Models\Specification;

class SyncProductSpecificationAction
{
    public function execute(Product $product, array $specs): void
    {
        if (empty($specs)) return;

        $product->specifications()->each(function ($spec) {
            $spec->translations()->delete();
            $spec->delete();
        });

        foreach ($specs as $specData) {

            $spec = Specification::create([
                'product_id' => $product->id
            ]);

            foreach ($specData as $locale => $values) {

                if (empty($values['key']) || empty($values['value'])) continue;

                $spec->translations()->create([
                    'locale' => $locale,
                    'key' => $values['key'],
                    'value' => $values['value']
                ]);
            }
        }
    }
}