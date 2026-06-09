<?php

namespace App\Domain\Product\DTO;

use App\Domain\Product\Services\SlugGeneratorService;

class ProductBasicInfoDTO
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly ?string $sku = null,
        
    ) {}
}