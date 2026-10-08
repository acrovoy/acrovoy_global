<?php

namespace App\Domain\Product\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Language;
use App\Models\Country;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\ShippingTemplate;
use Illuminate\Support\Facades\Auth;

use App\Models\ProductVariantItem;

use App\Domain\Payment\Models\PaymentMethod;
use App\Domain\Payment\Models\PaymentTerm;

use App\Services\Company\ActiveContextService;

class ProductEditQueryService
{
    public function getEditViewData(Product $product): array
{
    

    // 🔹 Eager load всех нужных связей
    $product->load([
    'translations',
    'category',
    'materials',
    'priceTiers',
    'shippingTemplates',
    'paymentMethods.translations',
    'paymentTerms.translations',
    'variantGroup.items.product',
    'variantGroup.items.media',
]);

    $languages = Language::where('is_active', true)->get();

    // 🔹 Получаем parent item
    $parentItem = ProductVariantItem::where('product_id', $product->id)->first();

    // 🔹 Получаем все остальные варианты
    $otherVariants = $product->variantGroup?->items
        ->where('product_id', '!=', $product->id)
        ->values();

    // 🔹 Собираем коллекцию для Blade
    $variants = collect();
    if ($parentItem) $variants->push($parentItem);
    $variants = $variants->merge($otherVariants);

    // 🔹 Продукты поставщика
    $products = $this->getSupplierProducts();

    return [
        'product' => $product,
        'categories' => Category::all(),
        'languages' => $languages,
        'countries' => Country::withCurrentTranslation()->get(),
        'shippingTemplates' => $this->getShippingTemplates(),
        'defaultShippingTemplate' => $this->getDefaultShippingTemplate(),
        'productShippingIds' => $product->shippingTemplates->pluck('id')->toArray(),
        'materialsPrepared' => $this->prepareMaterials($languages),
        'selectedMaterials' => $product->materials->pluck('id')->toArray(),
        'translations' => $this->prepareTranslations($product, $languages),
        'variants' => $variants,
        'products' => $products,
        'paymentMethods' => PaymentMethod::where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get(),

        'paymentTerms' => PaymentTerm::where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get(),

        'productPaymentMethodIds' => $product->paymentMethods
            ->pluck('id')
            ->toArray(),

        'productPaymentTermIds' => $product->paymentTerms
            ->pluck('id')
            ->toArray(),
            ];
}



public function getSupplierProducts()
{

$ActiveContext = app(ActiveContextService::class);
    $supplierId = $ActiveContext->supplierId();
    

   

    return Product::with('translations')
        ->where('supplier_id', $supplierId)
        ->get();
}

private function authorizeProduct(Product $product): void
{
    $ActiveContext = app(ActiveContextService::class);

    abort_if(!$ActiveContext->isCompany(), 403);
   abort_if($product->supplier_id !== $ActiveContext->supplierId(), 403);
}

    private function getShippingTemplates()
{
    $context = app(ActiveContextService::class);

    

    $supplierId = $context->supplierId();
    
    return ShippingTemplate::where('provider_id', $supplierId)
        ->with('translations')
        ->get();
}

    private function getDefaultShippingTemplate()
{
    return ShippingTemplate::with('translations')
        ->where('provider_type', \App\Models\LogisticCompany::class)
        ->where('provider_id', 1)
        ->first();
}

    private function prepareMaterials($languages)
{
    $context = app(ActiveContextService::class);
    $supplierId = $context->supplierId();

    $materials = Material::with([
        'translations',
        'materialGroup',
        'photo',
    ])->get();

    $result = [];

    foreach ($materials as $material) {

        $group = $material->materialGroup;

        if (!$group) {
            continue;
        }

        /*
         * =========================================================
         * MATERIAL VISIBILITY
         * =========================================================
         *
         * Supplier materials:
         *   is_custom = 1
         *   owner_type = Supplier
         *   owner_id = current supplier
         *
         * System materials:
         *   is_custom = 0
         *   owner_type = null
         *   owner_id = null
         *
         * Materials belonging to another supplier are ignored.
         */

        $isSupplierMaterial =
            (bool) $group->is_custom
            && $group->owner_type === Supplier::class
            && (int) $group->owner_id === (int) $supplierId;

        $isSystemMaterial =
            !(bool) $group->is_custom
            && empty($group->owner_type)
            && empty($group->owner_id);

        if (!$isSupplierMaterial && !$isSystemMaterial) {
            continue;
        }

        $data = [
            'id' => $material->id,

            'translations' => [],

            'group' => [
                'id' => $group->id,
                'name' => $group->translatedName(),
                'description' => $group->translatedDescription(),
                'is_custom' => (bool) $group->is_custom,
                'owner_type' => $group->owner_type,
                'owner_id' => $group->owner_id,
            ],

            'photo' => $material->photo
                ? [
                    'cdn_url' => $material->photo->cdn_url,
                ]
                : null,
        ];

        foreach ($languages as $language) {

            $translation = $material->translations
                ->firstWhere('locale', $language->code);

            $data['translations'][$language->code] = [
                'name' => $translation->name ?? '',
            ];
        }

        $result[] = $data;
    }

    /*
     * =========================================================
     * SORTING
     * =========================================================
     *
     * 1. Current supplier groups
     * 2. System groups
     *
     * Then:
     *  - group name
     *  - material name
     */

    usort($result, function ($a, $b) {

        $aCustom = $a['group']['is_custom'] ? 0 : 1;
        $bCustom = $b['group']['is_custom'] ? 0 : 1;

        if ($aCustom !== $bCustom) {
            return $aCustom <=> $bCustom;
        }

        $aGroupName = mb_strtolower(
            $a['group']['name'] ?? ''
        );

        $bGroupName = mb_strtolower(
            $b['group']['name'] ?? ''
        );

        $groupCompare = $aGroupName <=> $bGroupName;

        if ($groupCompare !== 0) {
            return $groupCompare;
        }

        $aName = mb_strtolower(
            $a['translations'][app()->getLocale()]['name']
                ?? $a['translations']['en']['name']
                ?? ''
        );

        $bName = mb_strtolower(
            $b['translations'][app()->getLocale()]['name']
                ?? $b['translations']['en']['name']
                ?? ''
        );

        return $aName <=> $bName;
    });

    return $result;
}

    private function prepareTranslations($product, $languages)
    {
        $result = [];

        foreach ($languages as $language) {

            $translation = $product->translations
                ->firstWhere('locale', $language->code);

            $result[$language->code] = [
                'name' => $translation->name ?? '',
                'undername' => $translation->undername ?? '',
                'description' => $translation->description ?? '',
            ];
        }

        return $result;
    }

    
}