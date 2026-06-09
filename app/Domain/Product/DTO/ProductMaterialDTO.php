<?php

namespace App\Domain\Product\DTO;


class ProductMaterialDTO
{
    public function __construct(
        
        public readonly string $materialsSelected = '',
        
    ) {}
}