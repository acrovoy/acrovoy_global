<?php

namespace App\Domain\Product\Services;

use App\Models\ProductVariantGroup;
use App\Models\Product;

class VariantGroupService
{

public function createGroup(): int
{
    return ProductVariantGroup::create()->id;
}
}