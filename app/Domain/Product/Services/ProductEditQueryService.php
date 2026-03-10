<?php

namespace App\Domain\Product\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Language;
use App\Models\Country;
use App\Models\Material;
use App\Models\ShippingTemplate;
use Illuminate\Support\Facades\Auth;

use App\Models\ProductVariantItem;

class ProductEditQueryService
{
    public function getEditViewData(Product $product): array
{
    $this->authorizeProduct($product);

    // 🔹 Eager load всех нужных связей, включая варианты и их медиа
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

    // 🔹 Получаем варианты из группы
    $variants = $product->variantGroup?->items ?? collect();

    // 🔹 Добавляем родительский продукт в варианты, если его нет
    if (!$variants->contains(fn($item) => $item->product_id === $product->id)) {
        $variants->prepend(
            new ProductVariantItem([
                'product_id' => $product->id,
                'title' => $product->name,
                'media_id' => $product->variantPreview?->id // preview для родителя
            ])
        );
    }

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
        'variants' => $variants, // уже с родителем и загруженным media
    ];
}

    private function authorizeProduct(Product $product): void
    {
        abort_if(
            $product->supplier_id !== Auth::user()?->supplier?->id,
            403
        );
    }

    private function getShippingTemplates()
    {
        $supplier = Auth::user()->supplier;

        return ShippingTemplate::where('manufacturer_id', $supplier->id)
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