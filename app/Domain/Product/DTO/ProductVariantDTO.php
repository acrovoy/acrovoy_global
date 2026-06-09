<?php

namespace App\Domain\Product\DTO;

use App\Domain\Product\Services\SlugGeneratorService;

class ProductVariantDTO
{
    public function __construct(
        
        public readonly ?int $leadTime = null,
        public readonly bool $customization = false,
        
    ) {}
}