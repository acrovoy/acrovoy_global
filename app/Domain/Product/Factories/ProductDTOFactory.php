<?php

namespace App\Domain\Product\Factories;

use App\Domain\Product\DTO\ProductDTO;
use App\Domain\Product\DTO\ProductMoqDTO;
use App\Domain\Product\DTO\ProductCategoryDTO;
use App\Domain\Product\DTO\ProductCountryDTO;
use App\Domain\Product\DTO\ProductVariantDTO;
use App\Domain\Product\DTO\ProductBasicInfoDTO;
use App\Domain\Product\Services\SlugGeneratorService;
use App\Services\Company\ActiveContextService;

class ProductDTOFactory
{
    public function __construct(
        private SlugGeneratorService $slugService,
        private ActiveContextService $context
    ) {}

    public function fromRequest($request): ProductDTO
    {
        $defaultLocale = array_key_first($request->name);

        $name = $request->name[$defaultLocale] ?? '';
        $sku = $request->sku ?: '';
        $slug = $this->slugService->generate($name);

        return new ProductDTO(
            slug: $slug,
            name: $name,
            sku: $sku,
            supplierId: $this->context->id(),
            supplierType: $this->context->type(),
            categoryId: $request->category,
            moq: $request->moq,
            leadTime: $request->lead_time,
            customization: $request->customization === 'available',
            countryId: $request->country_id,
            createdBy: auth()->id()
        );
    }

    public function fromUpdateBasicRequest($request): ProductBasicInfoDTO
    {
        $defaultLocale = array_key_first($request->name ?? []);

        $name = $request->name[$defaultLocale] ?? '';
        $sku = $request->sku ?: '';
        $slug = $this->slugService->generate($name);

        return new ProductBasicInfoDTO(
            slug: $slug,
            name: $name,
            sku: $sku,
            
        );
    }

    public function fromUpdateCategoryRequest($request): ProductCategoryDTO
    {

    $attributes = $request->input('attributes', []);

    if ($attributes instanceof \Symfony\Component\HttpFoundation\ParameterBag) {
    $attributes = $attributes->all();
};


        return new ProductCategoryDTO(
    categoryId: $request->category,
    attributes: $attributes,
);
    }


    public function fromUpdateMoqRequest($request): ProductMoqDTO
    {


     return new ProductMoqDTO(
    moq: $request->moq,
);

    }


    public function fromUpdateCountryRequest($request): ProductCountryDTO
    {


     return new ProductCountryDTO(
    countryId: $request->country_id,
);

    }


    public function fromUpdateVariantRequest($request): ProductVariantDTO
    {


     return new ProductVariantDTO(
    leadTime: $request->lead_time,
    customization: $request->customization,
);

    }

    public function fromUpdateRequest($request): ProductDTO
    {
        $defaultLocale = array_key_first($request->name ?? []);

        $name = $request->name[$defaultLocale] ?? '';
        $sku = $request->sku ?: '';
        $slug = $this->slugService->generate($name);

        return new ProductDTO(
            slug: $slug,
            name: $name,
            sku: $sku,
            supplierId: $this->context->id(),
            supplierType: $this->context->type(),
            categoryId: $request->category,
            moq: $request->moq,
            leadTime: $request->lead_time,
            customization: $request->customization === 'available',
            countryId: $request->country_id,
            createdBy: auth()->id()
        );
    }
}