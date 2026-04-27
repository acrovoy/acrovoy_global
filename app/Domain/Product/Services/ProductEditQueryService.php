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

use App\Services\Company\ActiveContextService;

class ProductEditQueryService
{
    public function getEditViewData(Product $product): array
{
    $this->authorizeProduct($product);

    // 🔹 Eager load всех нужных связей
    $product->load([
        'translations',
        'category',
        'materials',
        'priceTiers',
        'shippingTemplates',
        'specifications.translations',
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
        'specsTranslations' => $this->prepareSpecs($product, $languages),
        'variants' => $variants,
        'products' => $products,
    ];
}



public function getSupplierProducts()
{
    $supplierId = auth()->user()?->supplier?->id;

    if (!$supplierId) {
        return collect();
    }

    return Product::with('translations')
        ->where('supplier_id', $supplierId)
        ->get();
}

private function authorizeProduct(Product $product): void
{
    $context = app(ActiveContextService::class);

    abort_if(!$context->isCompany(), 403);
    abort_if($context->type() !== Supplier::class, 403);
    abort_if($product->supplier_id !== $context->id(), 403);
}

    private function getShippingTemplates()
    {
        $context = app(ActiveContextService::class);

    abort_if(!$context->isCompany(), 403);

    $supplierId = $context->id();

        return ShippingTemplate::where('manufacturer_id', $supplierId)
            ->with('translations')
            ->get();
    }

    private function getDefaultShippingTemplate()
    {
        return ShippingTemplate::with('translations')
            ->where('logistic_company_id', 1)
            ->first();
    }

    private function prepareMaterials($languages)
    {
        $materials = Material::with('translations')->get();

        $result = [];

        foreach ($materials as $material) {

            $data = [
                'id' => $material->id,
                'translations' => []
            ];

            foreach ($languages as $language) {
                $translation = $material->translations
                    ->firstWhere('locale', $language->code);

                $data['translations'][$language->code] = [
                    'name' => $translation->name ?? ''
                ];
            }

            $result[] = $data;
        }

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

    private function prepareSpecs($product, $languages)
    {
        $result = [];

        foreach ($languages as $language) {

            $result[$language->code] = [];

            foreach ($product->specifications as $i => $spec) {

                $translation = $spec->translations
                    ->firstWhere('locale', $language->code);

                $result[$language->code][$i] = [
                    'key' => $translation->key ?? '',
                    'value' => $translation->value ?? '',
                ];
            }
        }

        return $result;
    }
}