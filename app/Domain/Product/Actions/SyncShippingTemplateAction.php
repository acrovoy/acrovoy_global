<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;

class SyncShippingTemplateAction
{
    public function execute(Product $product, array $templateIds): void
    {
        $product->shippingTemplates()->sync($templateIds);
    }
}