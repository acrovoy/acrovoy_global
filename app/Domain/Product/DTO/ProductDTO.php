<?php

namespace App\Domain\Product\DTO;

use App\Domain\Product\Services\SlugGeneratorService;

class ProductDTO
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly ?string $sku = null,
        public readonly int $supplierId,
        public readonly string $supplierType,
        public readonly ?int $createdBy = null,
        public readonly ?int $categoryId = null,
        public readonly ?int $moq = null,
        public readonly ?int $leadTime = null,
        public readonly bool $customization = false,
        public readonly ?int $countryId = null,
    ) {}
}