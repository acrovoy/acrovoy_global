<?php

namespace App\Domain\Product\Services;

use App\Models\Product;
use App\Models\ProductVariantGroup;
use App\Models\ProductVariantItem;
use App\Models\Category;
use App\Models\Language;
use App\Models\Country;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\ShippingTemplate;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use App\Domain\Payment\Models\PaymentMethod;
use App\Domain\Payment\Models\PaymentTerm;
use App\Domain\Returns\Models\ReturnPolicy;
use App\Domain\Returns\Models\ProductReturnPolicy;
use App\Domain\Returns\Models\SupplierReturnPolicySetting;

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

        // Определяем оригинальный товар.
        $originalProductId = $product->original_product_id ?? $product->id;

        // Загружаем оригинал, если редактируется временная копия.
        $originalProduct = $product->original_product_id
            ? Product::findOrFail($originalProductId)
            : $product;

        // Группа оригинала используется для первоначального отображения.
        $sourceGroupId = $originalProduct->variant_group_id;

        // Проверяем, существует ли отдельная временная группа копии.
        $editingGroup = $product->original_product_id && $product->variant_group_id
            ? ProductVariantGroup::find($product->variant_group_id)
            : null;

        $isStagingGroup = $editingGroup
            && (int) ($editingGroup->metadata['editing_copy_of'] ?? 0)
                === (int) $originalProductId;

        // Если временная группа уже создана, показываем её.
        // Иначе показываем варианты оригинала.
        $variantGroupId = $isStagingGroup
            ? $editingGroup->id
            : $sourceGroupId;

        $groupItems = $variantGroupId
            ? ProductVariantItem::with(['product.translations', 'media'])
                ->where('variant_group_id', $variantGroupId)
                ->orderBy('sort_order')
                ->get()
            : collect();

        // Родитель — оригинальный товар, дочерние элементы — остальные товары.
        $parentItem = $groupItems->firstWhere('product_id', $originalProductId);

        $otherVariants = $groupItems
            ->where('product_id', '!=', $originalProductId)
            ->values();

        $variants = collect();

        if ($parentItem) {
            $variants->push($parentItem);
        }

        $variants = $variants->merge($otherVariants);






    // 🔹 Продукты поставщика
    $products = $this->getSupplierProducts();

    /*
    |--------------------------------------------------------------------------
    | RETURNS & REFUNDS
    |--------------------------------------------------------------------------
    */

    // 🔹 Available return policies
    $returnPolicies = ReturnPolicy::query()
        ->with([
            'translations',
            'reasons.translations',
            'resolutions.translations',
        ])
        ->where('is_active', true)
        ->orderByDesc('is_default')
        ->orderBy('name')
        ->orderBy('id')
        ->get();

    // 🔹 Current product return policy
    $productReturnPolicy = ProductReturnPolicy::query()
        ->with([
            'returnPolicy.translations',
            'returnPolicy.reasons.translations',
            'returnPolicy.resolutions.translations',
        ])
        ->where('product_id', $product->id)
        ->first();

    /*
    |--------------------------------------------------------------------------
    | SUPPLIER DEFAULT RETURN POLICY
    |--------------------------------------------------------------------------
    */

    $identity = app(ActiveContextService::class)->identity();

    $supplierDefaultReturnPolicyId = null;

    if (!empty($identity['entity_id'])) {

        $supplierDefaultReturnPolicyId = SupplierReturnPolicySetting::query()
            ->where('supplier_id', $identity['entity_id'])
            ->value('default_return_policy_id');
    }


    /*
|--------------------------------------------------------------------------
| SUPPLIER DEFAULT PAYMENT METHODS
|--------------------------------------------------------------------------
*/

$supplierId = app(ActiveContextService::class)->supplierId();

$defaultPaymentMethodIds = $supplierId
    ? DB::table('supplier_payment_methods')
        ->where('supplier_id', $supplierId)
        ->orderBy('sort_order')
        ->pluck('payment_method_id')
        ->map(fn ($id) => (int) $id)
        ->toArray()
    : [];

    

    $supplierId = app(ActiveContextService::class)->supplierId();

$defaultPaymentTermIds = $supplierId
    ? DB::table('supplier_payment_settings')
        ->where('supplier_id', $supplierId)
        ->whereNotNull('default_payment_term_id')
        ->pluck('default_payment_term_id')
        ->map(fn ($id) => (int) $id)
        ->toArray()
    : [];


    $isNewProduct = !$product->exists;

$defaultShippingTemplateIds = $this->getDefaultShippingTemplateIds();

$productShippingIds = $product->shippingTemplates
    ->pluck('id')
    ->map(fn ($id) => (int) $id)
    ->all();



    $context = app(ActiveContextService::class);
$supplierId = $context->supplierId();




    return [
        'product' => $product,

        'originalProductId' => $originalProductId,

        'categories' => Category::all(),

        'languages' => $languages,

        'countries' => Country::withCurrentTranslation()->get(),

        'shippingTemplates' => $this->getShippingTemplates(),

        'defaultShippingTemplate' => $this->getDefaultShippingTemplate(),

        'productShippingIds' => $product->shippingTemplates
            ->pluck('id')
            ->toArray(),

        'paymentMethods' => PaymentMethod::where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get(),

        'defaultPaymentMethodIds' => $defaultPaymentMethodIds,

        'productPaymentMethodIds' => $product->paymentMethods
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray(),

        'defaultPaymentTermIds' => $defaultPaymentTermIds,

        'productPaymentTermIds' => $product->paymentTerms
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray(),

        'materialsPrepared' => $this->prepareMaterials($languages),

        'selectedMaterials' => $product->materials
            ->pluck('id')
            ->toArray(),

        'translations' => $this->prepareTranslations(
            $product,
            $languages
        ),

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

        /*
        |--------------------------------------------------------------------------
        | RETURNS & REFUNDS
        |--------------------------------------------------------------------------
        */

        'returnPolicies' => $returnPolicies,

        'productReturnPolicy' => $productReturnPolicy,

        'supplierDefaultReturnPolicyId' => $supplierDefaultReturnPolicyId,

        /*
        |--------------------------------------------------------------------------
        | SHIPPING TEMPLATES
        |--------------------------------------------------------------------------
        */



        'shippingTemplates' => $this->getShippingTemplates(),

        'defaultShippingTemplate' => $this->getDefaultShippingTemplate(),

        'defaultShippingTemplateIds' => $defaultShippingTemplateIds,

        'productShippingIds' => $productShippingIds,

        'isNewProduct' => $isNewProduct,


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

    if (!$supplierId) {
        return collect();
    }

    return ShippingTemplate::query()
        ->where('provider_type', Supplier::class)
        ->where('provider_id', $supplierId)
        ->where('is_active', true)
        ->with([
            'translations',
            'deliveryOptions.deliveryType',
        ])
        ->orderByDesc('is_default')
        ->orderBy('id')
        ->get();
}

private function getDefaultShippingTemplateIds(): array
{
    $supplierId = app(ActiveContextService::class)->supplierId();

    if (!$supplierId) {
        return [];
    }

    return ShippingTemplate::query()
        ->where('provider_type', Supplier::class)
        ->where('provider_id', $supplierId)
        ->where('is_active', true)
        ->where('is_default', true)
        ->pluck('id')
        ->map(fn ($id) => (int) $id)
        ->all();
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