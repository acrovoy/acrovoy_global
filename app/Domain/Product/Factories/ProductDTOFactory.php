<?php

namespace App\Domain\Product\Factories;

use App\Domain\Product\DTO\ProductDTO;
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