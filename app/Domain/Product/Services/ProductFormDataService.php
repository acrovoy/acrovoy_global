<?php

namespace App\Domain\Product\Services;

use App\Models\Category;
use App\Models\Material;
use App\Models\Country;
use App\Models\Supplier;
use App\Models\ShippingTemplate;
use App\Models\Attribute;

use App\Services\Company\ActiveContextService;

class ProductFormDataService
{
    public function getCreateFormData(): array
    {
        $locale = app()->getLocale();

        $context = app(ActiveContextService::class);

        

        $supplierId = $context->id();
        $supplierType = $context->type();

        $ShippingTe111mplate = ShippingTemplate::with('translations')
                ->where('provider_type', 'App\Models\LogisticCompany')
                ->where('provider_id', 1)
                ->first();


        return [
            'categories' => Category::all(),

            'materials' => Material::with(['translations' => function ($q) use ($locale) {
                $q->where('locale', $locale);
            }])->get(),

            'countries' => Country::withCurrentTranslation()
                ->where('is_active', true)
                ->get(),

            'shippingTemplates' => ShippingTemplate::where('provider_type', $supplierType)
                ->where('provider_id', $supplierId)
                ->with('translations')
                ->get(),

            'defaultShippingTemplate' => ShippingTemplate::with('translations')
                ->where('provider_type', 'App\Models\LogisticCompany')
                ->where('provider_id',1)
                ->first(),

            'customAttributes' => Attribute::query()
                ->where('entity_type', 'product')
                ->where('is_custom', 1)
                ->where('owner_type', 'App\Models\Supplier')
                ->where('owner_id', $supplierId)
                ->with([
                    'translations',
                    'options.translations'
                ])
                ->get(),
        ];
    }
}
