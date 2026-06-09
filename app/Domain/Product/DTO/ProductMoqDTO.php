<?php

namespace App\Domain\Product\DTO;

use App\Domain\Product\Services\SlugGeneratorService;

class ProductMoqDTO
{
    public function __construct(
       
        public readonly ?int $moq = null,
     
    ) {}
}