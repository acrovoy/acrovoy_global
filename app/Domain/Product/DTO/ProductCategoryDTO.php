<?php

namespace App\Domain\Product\DTO;


class ProductCategoryDTO
{
    public function __construct(
        public readonly ?int $categoryId = null,
        public readonly array $attributes = [],
        
    ) {}
}