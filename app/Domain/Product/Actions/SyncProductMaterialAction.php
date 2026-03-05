<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;

class SyncProductMaterialAction
{
    public function execute(Product $product, array $materialIds): void
    {
        $product->materials()->sync($materialIds);
    }
}