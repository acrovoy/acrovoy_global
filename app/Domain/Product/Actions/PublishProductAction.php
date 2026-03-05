<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Domain\Product\Events\ProductPublishedEvent;

class PublishProductAction
{
    public function execute(Product $product): Product
    {
        $product->status = 'published';
        $product->save();

        ProductPublishedEvent::dispatch($product);

        return $product;
    }
}