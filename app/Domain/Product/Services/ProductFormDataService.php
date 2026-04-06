<?php

namespace App\Domain\Product\Services;

use App\Models\Category;
use App\Models\Material;
use App\Models\Country;
use App\Models\Supplier;
use App\Models\ShippingTemplate;

class ProductFormDataService
{
    public function getCreateFormData(): array
    {
        $locale = app()->getLocale();

        $supplier = Supplier::where('user_id', auth()->id())->firstOrFail();

        return [
            'categories' => Category::all(),

            'materials' => Material::with(['translations' => function ($q) use ($locale) {
                $q->where('locale', $locale);
            }])->get(),

            'countries' => Country::withCurrentTranslation()
                ->where('is_active', true)
                ->get(),

            'shippingTemplates' => ShippingTemplate::where('manufacturer_id', $supplier->id)
                ->with('translations')
                ->get(),

            'defaultShippingTemplate' => ShippingTemplate::with('translations')
                ->where('logistic_company_id', 1)
                ->first(),
        ];
    }

    
}