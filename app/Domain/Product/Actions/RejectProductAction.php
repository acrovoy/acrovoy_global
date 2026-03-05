<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Domain\Product\Events\ProductRejectedEvent;

class RejectProductAction
{
    public function execute(Product $product, ?string $reason = null): Product
    {
        $product->status = 'rejected';
        $product->reject_reason = $reason;
        $product->save();

        ProductRejectedEvent::dispatch($product, $reason);

        return $product;
    }
}