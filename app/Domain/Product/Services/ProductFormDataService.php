<?php

namespace App\Domain\Product\Services;

use App\Models\Category;
use App\Models\Material;
use App\Models\Country;
use App\Models\Supplier;
use App\Models\ShippingTemplate;

use App\Services\Company\ActiveContextService;

class ProductFormDataService
{
    public function getCreateFormData(): array
    {
        $locale = app()->getLocale();

        $context = app(ActiveContextService::class);

        abort_if(!$context->isCompany(), 403);

        $supplierId = $context->id();

        return [
            'categories' => Category::all(),

            'materials' => Material::with(['translations' => function ($q) use ($locale) {
                $q->where('locale', $locale);
            }])->get(),

            'countries' => Country::withCurrentTranslation()
                ->where('is_active', true)
                ->get(),

            'shippingTemplates' => ShippingTemplate::where('manufacturer_id', $supplierId)
                ->with('translations')
                ->get(),

            'defaultShippingTemplate' => ShippingTemplate::with('translations')
                ->where('logistic_company_id', 1)
                ->first(),
        ];
    }

    
}