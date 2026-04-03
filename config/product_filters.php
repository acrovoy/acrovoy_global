<?php

return [
    'material'       => \App\Domain\Filters\Product\MaterialFilter::class,
    'min_price'      => \App\Domain\Filters\Product\PriceFilter::class,
    'max_price'      => \App\Domain\Filters\Product\PriceFilter::class,
    'min_moq'        => \App\Domain\Filters\Product\MOQFilter::class,
    'sold_from'      => \App\Domain\Filters\Product\SoldFilter::class,
    'min_lead_time'  => \App\Domain\Filters\Product\LeadTimeFilter::class,
    'country'        => \App\Domain\Filters\Product\CountryFilter::class,
    'supplier_type'  => \App\Domain\Filters\Product\SupplierTypeFilter::class,
    'category' => \App\Domain\Filters\Product\CategoryFilter::class,
    'attributes' => \App\Domain\Filters\Product\AttributeFilter::class,
];