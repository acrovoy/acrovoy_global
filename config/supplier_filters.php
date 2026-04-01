<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supplier Filters Pipeline Config
    |--------------------------------------------------------------------------
    |
    | Здесь регистрируются все фильтры поставщиков.
    | Формат:
    |
    | 'request_param' => FilterClass::class
    |
    | request_param должен совпадать с параметром в Request.
    |
    */

    'country' => \App\Domain\Filters\Supplier\CountryFilter::class,

    'export_market' => \App\Domain\Filters\Supplier\ExportMarketFilter::class,



    // Тип поставщика (trusted / verified / premium)
    // ?supplier_type[]=trusted
    'supplier_type' => \App\Domain\Filters\Supplier\SupplierTypeFilter::class,


    // Категория товаров поставщика
    // ?category=chairs
    'category' => \App\Domain\Filters\Supplier\CategoryFilter::class,


    // Стаж на платформе
    // ?years=5
    'years' => \App\Domain\Filters\Supplier\YearsFilter::class,

     /*
    |--------------------------------------------------------------------------
    | Planned Filters (можно подключать по мере реализации)
    |--------------------------------------------------------------------------
    */




];