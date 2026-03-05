<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;

class SyncProductMediaAction
{
    public function execute(Product $product, array $files): void
    {
        if (empty($files)) return;

        foreach ($files as $index => $file) {

            if (!$file) continue;

            $path = $file->store('products', 'public');

            $product->images()->create([
                'image_path' => $path,
                'sort_order' => $index,
                'is_main' => $index === 0
            ]);
        }
    }
}