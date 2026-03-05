<?php

namespace App\Domain\Product\DTO;

use App\Domain\Product\Services\SlugGeneratorService;

class ProductDTO
{
    public function __construct(
        public string $slug,
        public string $name,
        public int $supplierId,
        public int $categoryId,
        public ?int $moq = null,
        public ?int $leadTime = null,
        public bool $customization = false,
        public ?int $countryId = null,
    ) {}

    public static function fromRequest($request): self
{
    $defaultLocale = array_key_first($request->name);

    $name = $request->name[$defaultLocale] ?? '';

    $slug = app(SlugGeneratorService::class)
        ->generate($name);

    return new self(
        slug: $slug,
        name: $name,
        supplierId: auth()->user()->supplier->id,
        categoryId: $request->category,
        moq: $request->moq,
        leadTime: $request->lead_time,
        customization: $request->customization === 'available',
        countryId: $request->country_id
    );
}

public static function fromUpdateRequest($request): self
{
    $defaultLocale = array_key_first($request->name ?? []);

    $name = $request->name[$defaultLocale] ?? '';

    $slug = app(SlugGeneratorService::class)
        ->generate($name);

    return new self(
        slug: $slug,
        name: $name,
        supplierId: auth()->user()->supplier->id,
        categoryId: $request->category,
        moq: $request->moq,
        leadTime: $request->lead_time,
        customization: $request->customization === 'available',
        countryId: $request->country_id
    );
}

}