<?php

namespace App\Domain\Product\DTO;

use App\Domain\Product\Services\SlugGeneratorService;

class ProductCountryDTO
{
    public function __construct(
       
        public readonly ?int $countryId = null,
     
    ) {}
}