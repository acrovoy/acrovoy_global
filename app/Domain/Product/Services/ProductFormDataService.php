<?php

namespace App\Domain\Product\Services;

use App\Models\Category;
use App\Models\Material;
use App\Models\Country;
use App\Models\ShippingTemplate;
use App\Models\Attribute;



class ProductFormDataService
{
    public function getCreateFormData($supplierId): array
    {
        $locale = app()->getLocale();

        
        return [
            'categories' => Category::all(),

            'materials' => Material::with(['translations' => function ($q) use ($locale) {
                $q->where('locale', $locale);
            }])->get(),

            'countries' => Country::withCurrentTranslation()
                ->where('is_active', true)
                ->get(),

            'shippingTemplates' => ShippingTemplate::where('provider_id', $supplierId)
                ->with('translations')
                ->get(),

            'defaultShippingTemplate' => ShippingTemplate::with('translations')
                ->where('provider_type', 'App\Models\LogisticCompany')
                ->where('provider_id',1)
                ->first(),

            'customAttributes' => Attribute::query()
                ->where('entity_type', 'product')
                ->where('is_custom', 1)
                ->where('owner_id', $supplierId)
                ->with([
                    'translations',
                    'options.translations'
                ])
                ->get(),
        ];
    }
}
