<?php

namespace App\Domain\Product\Events;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductDeletedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $productId
    ) {}
}