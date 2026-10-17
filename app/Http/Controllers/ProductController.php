<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Domain\Product\Factories\ProductDTOFactory;

/* === REQUEST === */
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\StoreProductRequest;

use Illuminate\Support\Facades\Log;


/* === SERVICES === */
use App\Domain\Product\Services\ProductFormDataService;
use App\Domain\Product\Services\ProductEditQueryService;
use App\Domain\Product\Services\ProductViewQueryService;
use App\Domain\Product\Services\ProductListQueryService;

/* === ACTIONS === */
use App\Domain\Product\Actions\DeleteProductAction;
use App\Domain\Product\Actions\UpdateProductBasicInfoAction;
use App\Domain\Product\Actions\UpdateProductCategoryAction;
use App\Domain\Product\Actions\UpdateProductMaterialsAction;
use App\Domain\Product\Actions\UpdateProductMediaAction;
use App\Domain\Product\Actions\UpdateProductMoqPriceAction;
use App\Domain\Product\Actions\UpdateProductCountryShippingAction;
use App\Domain\Product\Actions\UpdateProductVariantAction;
use App\Domain\Product\Actions\AttachProductVariantAction;
use App\Domain\Product\Actions\CreateProductEditingCopyAction;
use App\Domain\Product\Actions\DuplicateProductAction;

/* === MODELS === */
use App\Models\Product;

use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\Warehouse;
use App\Models\ProductAttributeValue;
use App\Models\ProductWarehouseStock;
use App\Models\ProductVariantItem;
use App\Models\ProductVariantGroup;

use App\Domain\Payment\Models\PaymentMethod;
use App\Domain\Payment\Models\PaymentTerm;

use App\Domain\Returns\Models\ReturnPolicy;
use App\Domain\Returns\Models\ProductReturnPolicy;

use App\Domain\Media\Services\MediaService;
use App\Domain\Media\DTO\UploadMediaDTO;

use App\Models\Country;

use App\Domain\Product\Actions\CreateProductAction;
use App\Domain\Product\Actions\SyncProductTranslationAction;

use App\Domain\Product\Actions\SyncProductPriceTierAction;

use App\Domain\Product\Actions\SyncProductAttributeAction;
use App\Domain\Product\Actions\SyncProductMaterialAction;
use App\Domain\Product\Actions\SyncShippingTemplateAction;
use App\Domain\Product\Actions\SyncProductCustomAttributeAction;

use App\Services\Company\ActiveContextService;


class ProductController extends Controller
{



    public function __construct(
        private ActiveContextService $context,
        private MediaService $mediaService,

    ) {}

    

public function index(Request $request, ProductListQueryService $service)
{
    $this->authorize('viewAny', Product::class);

    $entity = $this->context->entity();
    $supplierId = $entity->getKey();

    $filters = $request->validate([
        'search' => ['nullable', 'string', 'max:150'],
        'status' => [
            'nullable',
            'string',
            'in:draft,pending,approved,rejected',
        ],
        'category' => ['nullable', 'integer', 'min:1'],
        'user' => ['nullable', 'string', 'max:150'],
        'sort' => [
            'nullable',
            'string',
            'in:position,newest,oldest,name_asc,name_desc,status',
        ],
    ]);

    // Manual product order is the default.
    $filters['sort'] = $filters['sort'] ?? 'position';

    $products = $service->getSupplierProducts(
        $supplierId,
        $filters
    );

    $statistics = $service->getSupplierProductStatistics($supplierId);

    $warehouses = Warehouse::query()
        ->where('provider_id', $supplierId)
        ->where('provider_type', $entity::class)
        ->get();

    $categoryIds = Product::query()
        ->where('supplier_id', $supplierId)
        ->whereNull('original_product_id')
        ->where('status', '!=', 'archived')
        ->whereNotNull('category_id')
        ->distinct()
        ->pluck('category_id');

    $categories = \App\Models\Category::query()
        ->whereIn('id', $categoryIds)
        ->orderBy('name')
        ->get();

    return view('dashboard.supplier.products', [
        'products' => $products,
        'warehouses' => $warehouses,
        'categories' => $categories,
        'search' => $filters['search'] ?? '',
        'sort' => $filters['sort'],
        'status' => $filters['status'] ?? '',
        'categoryFilter' => $filters['category'] ?? '',
        'userFilter' => $filters['user'] ?? '',
        'addedCount' => $statistics['addedCount'],
        'publishedCount' => $statistics['publishedCount'],
        'inCatalogCount' => $statistics['inCatalogCount'],
        'notInCatalogCount' => $statistics['notInCatalogCount'],
        'productLimit' => $statistics['productLimit'],
    ]);
}




    public function show(string $slug, ProductViewQueryService $service)
    {
        return view('product.show', $service->getProductViewData($slug));
    }


    public function create(ProductFormDataService $service)
    {

        $this->authorize('create', Product::class);


        $supplier = $this->context->supplier();

        $products = Product::with('translations')
            ->where('supplier_id', $supplier->id)
            ->get();
        $availableAttributesGrouped = collect();

        $data = $service->getCreateFormData($supplier->id);

        return view('product.create.add-product', array_merge($data, [
            'steps' => 1,
            'countries' => Country::all(),
            'products' => $products,
            'availableAttributesGrouped' => $availableAttributesGrouped,
        ]));
    }






    public function store(
        Request $request,
        CreateProductAction $createProduct,
        SyncProductTranslationAction $translationAction,
        ProductDTOFactory $dtoFactory,
    ) {

        $this->authorize('create', Product::class);

        /*
        |-------------------------------------------------------------------------- 
        | Create Product
        |-------------------------------------------------------------------------- 
        */
        $productDTO = $dtoFactory->fromRequest($request);
        $product = $createProduct->execute($productDTO);

        /*
        |-------------------------------------------------------------------------- 
        | Translation Sync
        |-------------------------------------------------------------------------- 
        */
        $translationAction->execute(
            $product,
            $request->name,
            $request->undername,
            $request->description
        );

        return redirect()->route('supplier.products.edit-step', [
            'product' => $product->id,
            'step' => 2,
        ])
            ->with('success', 'Product created successfully. Please proceed to the next step to add more details.');
    }



    public function edit(
        Product $product,
        ProductEditQueryService $service,
        $step = 1
    ) {

        $this->authorize('update', $product);

        $entity = $this->context->entity();

        $ownerType = $entity::class;
        $ownerId = $entity->getKey();


        $availableAttributes = Attribute::query()
            ->where('entity_type', 'product')
            ->where('is_custom', 1)
            ->where('is_active', true)
            ->where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->get();

        $availableAttributesGrouped = $availableAttributes
            ->load('group')
            ->groupBy(fn($attr) => $attr->group?->name ?? 'General')
            ->sortBy(function ($attrs, $groupName) {
                return strtolower($groupName) === 'general' ? 0 : 1;
            });


        $groups = AttributeGroup::where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->get();


           $editingCopy = $product->original_product_id !== null
    ? (in_array($product->status, ['draft', 'rejected'], true) ? $product : null)
    : Product::query()
        ->where('original_product_id', $product->id)
        ->where('supplier_id', $product->supplier_id)
        ->whereIn('status', ['draft', 'rejected'])
        ->latest('id')
        ->first();

        $editData = $service->getEditViewData($product);

        $attachedIds = $product->attributes()->pluck('attributes.id')->toArray();

        $attachedAttributes = Attribute::whereIn('id', $attachedIds)->get();

        $customAttributes = $product->attributes()
            ->where('is_custom', 1)
            ->where('is_active', 1)
            ->with('group')
            ->get();


        return view(
            'product.edit.edit',
            $editData,
            [
                'steps' => $step,
                'customAttributes' => $customAttributes,
                'attachedAttributes' => $attachedAttributes,
                'attachedIds' => $attachedIds,
                'groups' => $groups,
                'availableAttributesGrouped' => $availableAttributesGrouped,
                'editingCopy' => $editingCopy,
            ]
        );
    }



    public function update(
        Request $request,
        Product $product,
        UpdateProductBasicInfoAction $basicInfoAction,
        UpdateProductCategoryAction $updateProductCategoryAction,
        UpdateProductMaterialsAction $updateProductMaterialsAction,
        UpdateProductMediaAction $updateProductMediaAction,
        UpdateProductMoqPriceAction $updateMoqPriceAction,
        UpdateProductCountryShippingAction $updateCountryShippingaction,
        UpdateProductVariantAction $updateProductVariantAction,
        SyncProductCustomAttributeAction $customAttributeAction,
        ProductDTOFactory $dtoFactory,
        $step = 1
    ) {

        $this->authorize('update', $product);


        $nextstep = $step + 1;

        if ($step == 1) {

            $dto = $dtoFactory->fromUpdateBasicRequest($request);

            $translations = [];
            if ($request->name) {
                foreach ($request->name as $locale => $name) {
                    $translations[$locale] = [
                        'name' => $name,
                        'undername' => $request->undername[$locale] ?? null,
                        'description' => $request->description[$locale] ?? null,
                    ];
                }
            }

            $basicInfoAction->execute(
                product: $product,
                data: $dto,
                translations: $translations,

            );


            return redirect()
                ->route('supplier.products.edit-step', [
                    'product' => $product->id,
                    'step' => $nextstep,
                ]);
        } elseif ($step == 2) {





            /*
    |--------------------------------------------------------------------------
    | VALIDATE REQUIRED CATEGORY ATTRIBUTES
    |--------------------------------------------------------------------------
    */

            $attributes = $request->input('attributes', []);

            $category = \App\Models\Category::find(
                $request->input('category')
            );

            if (!$category) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'category' => 'Please select a valid category.',
                    ])
                    ->withInput();
            }

            $requiredAttributes = $category
                ->attributes()
                ->wherePivot('is_required', true)
                ->get();

            $errors = [];

            foreach ($requiredAttributes as $attribute) {

                $attributeId = (string) $attribute->id;

                $value = $attributes[$attributeId] ?? null;

                /*
        |--------------------------------------------------------------------------
        | BOOLEAN
        |--------------------------------------------------------------------------
        */

                if ($attribute->type === 'boolean') {

                    if (!array_key_exists($attributeId, $attributes)) {
                        $errors["attributes.$attributeId"] =
                            "{$attribute->name} is required.";
                    }

                    continue;
                }

                /*
        |--------------------------------------------------------------------------
        | MULTISELECT
        |--------------------------------------------------------------------------
        */

                if ($attribute->type === 'multiselect') {

                    if (
                        !is_array($value) ||
                        empty(array_filter($value, fn($v) => $v !== null && $v !== ''))
                    ) {
                        $errors["attributes.$attributeId"] =
                            "{$attribute->name} is required.";
                    }

                    continue;
                }


                /*
|--------------------------------------------------------------------------
| MEASUREMENT
|--------------------------------------------------------------------------
*/

                if ($attribute->type === 'measurement') {

                    $measurement = $attributes[$attributeId] ?? null;

                    $value = is_array($measurement)
                        ? ($measurement['value'] ?? null)
                        : null;

                    $unitId = is_array($measurement)
                        ? ($measurement['unit_id'] ?? null)
                        : null;

                    if ($value === null || $value === '' || !is_numeric($value) || (float) $value <= 0) {

                        $errors["attributes.$attributeId.value"] =
                            "{$attribute->name} must be greater than 0.";
                    }

                    if (!$unitId) {

                        $errors["attributes.$attributeId.unit_id"] =
                            "{$attribute->name} unit is required.";
                    }

                    continue;
                }


                /*
        |--------------------------------------------------------------------------
        | SELECT / NUMBER / TEXT
        |--------------------------------------------------------------------------
        */

                if ($value === null || $value === '') {

                    $errors["attributes.$attributeId"] =
                        "{$attribute->name} is required.";
                }
            }

            if (!empty($errors)) {

                return redirect()
                    ->back()
                    ->withErrors($errors)
                    ->withInput();
            }


            $attributeUnits = $request->input('attribute_units', []);


            /*
    |--------------------------------------------------------------------------
    | SAVE CATEGORY + ATTRIBUTES
    |--------------------------------------------------------------------------
    */

            $dto = $dtoFactory->fromUpdateCategoryRequest($request);

            $updateProductCategoryAction->execute(
                product: $product,
                data: $dto,
            );

            return redirect()
                ->route('supplier.products.edit-step', [
                    'product' => $product->id,
                    'step' => $nextstep,
                ]);
        } elseif ($step == 3) {


            /*
            |--------------------------------------------------------------------------
            | CUSTOM ATTRIBUTES
            |--------------------------------------------------------------------------
            */
            if ($request->has('custom_attributes')) {
                $customAttributeAction->execute(
                    $product,
                    $request->input('custom_attributes')
                );
            }

           

            return redirect()
                ->route('supplier.products.edit-step', [
                    'product' => $product->id,
                    'step' => $nextstep,
                ]);
        } elseif ($step == 4) {



            $updateProductMediaAction->execute(
                product: $product,
                mediaFiles: $request->file('images', []),
                existingIds: $request->existing_ids ?? [],
                sortOrder: $request->sort_order ?? [],
                existingSortOrder: $request->existing_sort_order ?? [],
                isMain: $request->is_main ?? [],

            );



            return redirect()
                ->route('supplier.products.edit-step', [
                    'product' => $product->id,
                    'step' => $nextstep,
                ]);
        } elseif ($step == 5) {

            $dto = $dtoFactory->fromUpdateMoqRequest($request);

            $updateMoqPriceAction->execute(
                product: $product,
                data: $dto,
                priceTiers: $request->price_tiers ?? [],

            );


            return redirect()
                ->route('supplier.products.edit-step', [
                    'product' => $product->id,
                    'step' => $nextstep,
                ]);
        } elseif ($step == 6) {


        $product->update([
    'customization' => $request->boolean('customization'),
]);

             $updateProductMaterialsAction->execute(
                product: $product,
                materialsSelected: $request->materials_selected ?? '',

            );

            return redirect()
                ->route('supplier.products.edit-step', [
                    'product' => $product->id,
                    'step' => $nextstep,
                ]);
       } elseif ($step == 7) {

    if (
        $request->boolean('variants_present')
        || $request->has('variants')
    ) {
        DB::transaction(function () use ($request, $product) {

            $variantRows = collect($request->input('variants', []))
                ->map(function ($row, $index) use ($request) {
                    $row['image'] = $request->file(
                        "variants.{$index}.image"
                    );

                    return $row;
                })
                ->values();

            $isEditingCopy = $product->original_product_id !== null;

            /*
            |--------------------------------------------------------------------------
            | Для копии определяем, остались ли дочерние вариации
            |--------------------------------------------------------------------------
            |
            | Если дочерних вариаций нет, не сохраняем родительский товар
            | в staging-группу как вариацию самого себя.
            |
            */

            if ($isEditingCopy) {
                $hasChildVariants = $variantRows->contains(
                    fn ($row) => empty($row['is_primary'])
                );

                if (!$hasChildVariants) {
                    $variantRows = collect();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Определяем группу вариантов
            |--------------------------------------------------------------------------
            */

            $variantGroupId = null;

            if ($isEditingCopy) {
                $original = Product::query()
                    ->findOrFail($product->original_product_id);

                // Используем только staging-группу текущей копии.
                $currentGroup = $product->variant_group_id
                    ? ProductVariantGroup::find($product->variant_group_id)
                    : null;

                if (
                    $currentGroup
                    && (int) ($currentGroup->metadata['editing_copy_of'] ?? 0)
                        === (int) $original->id
                ) {
                    $variantGroupId = $currentGroup->id;
                }

                // Создаём staging-группу даже при отсутствии вариаций.
                if (!$variantGroupId) {
                    $group = ProductVariantGroup::create([
                        'name' => null,
                        'sort_order' => 0,
                        'metadata' => [
                            'editing_copy_of' => $original->id,
                            'variants_saved' => true,
                        ],
                    ]);

                    $variantGroupId = $group->id;

                    // Меняем только временную копию.
                    $product->update([
                        'variant_group_id' => $variantGroupId,
                    ]);
                } else {
                    $group = ProductVariantGroup::findOrFail(
                        $variantGroupId
                    );

                    $metadata = $group->metadata ?? [];
                    $metadata['variants_saved'] = true;

                    $group->update([
                        'metadata' => $metadata,
                    ]);
                }
            } else {
                // Для обычного товара используем его собственную группу.
                $variantGroupId = $product->variant_group_id;

                if (!$variantGroupId) {
                    $variantGroupId = ProductVariantItem::query()
                        ->where('product_id', $product->id)
                        ->value('variant_group_id');
                }

                if (!$variantGroupId && $variantRows->isNotEmpty()) {
                    $hasLinkedProducts = $variantRows->contains(
                        fn ($row) => !empty($row['linked_product_id'])
                    );

                    if ($hasLinkedProducts) {
                        $group = ProductVariantGroup::create([]);

                        $variantGroupId = $group->id;

                        $product->update([
                            'variant_group_id' => $variantGroupId,
                        ]);
                    }
                }

                // Обычный товар без группы и без вариантов.
                if (!$variantGroupId) {
                    return;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Загружаем только элементы текущей группы
            |--------------------------------------------------------------------------
            */

            $existingVariants = ProductVariantItem::query()
                ->where('variant_group_id', $variantGroupId)
                ->get()
                ->keyBy('id');

            $incomingIds = $variantRows
                ->pluck('id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->all();

            /*
            |--------------------------------------------------------------------------
            | Удаляем элементы, отсутствующие в отправленной форме
            |--------------------------------------------------------------------------
            */

            foreach ($existingVariants as $existingVariant) {
                if (!in_array(
                    (int) $existingVariant->id,
                    $incomingIds,
                    true
                )) {
                    $linkedProduct = $existingVariant->product;

                    $existingVariant->delete();

                    // При редактировании копии товары каталога не меняем.
                    if (
                        !$isEditingCopy
                        && $linkedProduct
                        && (int) $linkedProduct->variant_group_id
                            === (int) $variantGroupId
                    ) {
                        $linkedProduct->update([
                            'variant_group_id' => null,
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Создаём и обновляем элементы группы
            |--------------------------------------------------------------------------
            */

            foreach ($variantRows as $index => $variantData) {
                $variantId = !empty($variantData['id'])
                    ? (int) $variantData['id']
                    : null;

                // Разрешено обновлять только элементы своей группы.
                $variant = $variantId
                    ? $existingVariants->get($variantId)
                    : null;

                $linkedProductId = $variantData['linked_product_id'] ?? null;

                // Родительская строка копии ссылается на оригинал.
                if (
                    $isEditingCopy
                    && !empty($variantData['is_primary'])
                ) {
                    $linkedProductId = $product->original_product_id;
                }

                if (!$variant) {
                    $variant = new ProductVariantItem();
                    $variant->variant_group_id = $variantGroupId;
                }

                $variant->title = $variantData['title'] ?? null;
                $variant->product_id = $linkedProductId ?: null;
                $variant->sort_order = $index;
                $variant->save();

                // Только для обычного товара обновляем группы товаров.
                if (!$isEditingCopy && $linkedProductId) {
                    Product::query()
                        ->whereKey($linkedProductId)
                        ->update([
                            'variant_group_id' => $variantGroupId,
                        ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Загружаем изображение элемента
                |--------------------------------------------------------------------------
                */

                $image = $variantData['image'] ?? null;

                if (
                    $image instanceof \Illuminate\Http\UploadedFile
                    && $image->isValid()
                ) {
                    $media = $this->mediaService->upload(
                        new UploadMediaDTO(
                            file: $image,
                            model: $variant,
                            collection: 'product_variant_image',
                            sortOrder: 0,
                            isMain: false
                        )
                    );

                    $variant->update([
                        'media_id' => $media->id,
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Очистка группы обычного товара
            |--------------------------------------------------------------------------
            |
            | Поведение первоначального создания и редактирования обычного
            | товара не меняем.
            |
            */

            if (!$isEditingCopy) {
                $remainingCount = ProductVariantItem::query()
                    ->where('variant_group_id', $variantGroupId)
                    ->count();

                if ($remainingCount <= 1) {
                    $remainingVariants = ProductVariantItem::query()
                        ->where('variant_group_id', $variantGroupId)
                        ->get();

                    foreach ($remainingVariants as $variant) {
                        $linkedProduct = $variant->product;

                        $variant->delete();

                        if (
                            $linkedProduct
                            && (int) $linkedProduct->variant_group_id
                                === (int) $variantGroupId
                        ) {
                            $linkedProduct->update([
                                'variant_group_id' => null,
                            ]);
                        }
                    }

                    ProductVariantGroup::whereKey($variantGroupId)->delete();

                    $product->update([
                        'variant_group_id' => null,
                    ]);
                }
            }
        });
    }

    $dto = $dtoFactory->fromUpdateVariantRequest($request);

    $updateProductVariantAction->execute(
        product: $product,
        data: $dto,
    );

    return redirect()
        ->route('supplier.products.edit-step', [
            'product' => $product->id,
            'step' => $nextstep,
        ]);

} elseif ($step == 8) {

    $response = $this->saveFinishStep(
        $request,
        $product,
        $updateCountryShippingaction,
        $dtoFactory
    );

    if ($response) {
        return $response;
    }

    return redirect()
    ->route('supplier.products.index')
    ->with('success', 'Product details saved successfully.');
}

        return redirect()
            ->route('supplier.products.edit-step', [
                'product' => $product->id,
                'step' => $step,
            ]);
    }

    public function saveFinishStep(
    Request $request,
    Product $product,
    UpdateProductCountryShippingAction $updateCountryShippingAction,
    ProductDTOFactory $dtoFactory
) {
    $this->authorize('update', $product);

    $validated = $request->validate([
    'lead_time' => ['sometimes', 'nullable', 'integer', 'min:0'],
]);

    /*
    |--------------------------------------------------------------------------
    | SHIPPING DIMENSIONS
    |--------------------------------------------------------------------------
    */

    $shippingData = $request->input('shipping', []);

    if (!empty($shippingData)) {
        $product->shippingDimensions()->updateOrCreate(
            [
                'dimensionable_type' => Product::class,
                'dimensionable_id' => $product->id,
            ],
            [
                'length' => $shippingData['length'] ?? 0,
                'width' => $shippingData['width'] ?? 0,
                'height' => $shippingData['height'] ?? 0,
                'weight' => $shippingData['weight'] ?? 0,
                'package_type' => $shippingData['package_type'] ?? 'box',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | COUNTRY AND SHIPPING TEMPLATES
    |--------------------------------------------------------------------------
    */

    $dto = $dtoFactory->fromUpdateCountryRequest($request);

    $updateCountryShippingAction->execute(
        product: $product,
        data: $dto,
        shippingTemplates: $request->input('shipping_templates', [])
    );

    /*
    |--------------------------------------------------------------------------
    | PAYMENT METHODS
    |--------------------------------------------------------------------------
    */

    $product->paymentMethods()->sync(
        $request->input('payment_methods', [])
    );

    /*
    |--------------------------------------------------------------------------
    | PAYMENT TERMS
    |--------------------------------------------------------------------------
    */

    $product->paymentTerms()->sync(
    $request->filled('payment_term_id')
        ? [(int) $request->input('payment_term_id')]
        : []
);

    /*
    |--------------------------------------------------------------------------
    | RETURNS AND REFUNDS
    |--------------------------------------------------------------------------
    */

    $useSupplierDefault = $request->boolean('use_supplier_default');

    $returnPolicyId = null;

    if (!$useSupplierDefault) {
        $returnPolicyId = $request->input('return_policy_id');

        if ($returnPolicyId) {
            $returnPolicyExists = ReturnPolicy::query()
                ->where('id', $returnPolicyId)
                ->where('is_active', true)
                ->exists();

            if (!$returnPolicyExists) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'return_policy_id' => 'Please select a valid return policy.',
                    ])
                    ->withInput();
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE PRODUCT RETURN POLICY
    |--------------------------------------------------------------------------
    */

    ProductReturnPolicy::updateOrCreate(
        [
            'product_id' => $product->id,
        ],
        [
            'return_policy_id' => $returnPolicyId,
            'use_supplier_default' => $useSupplierDefault,
            'returnable' => null,
            'return_window_days' => null,
            'return_shipping_payer' => null,
            'restocking_fee_enabled' => null,
            'restocking_fee_percent' => null,
            'custom_products_returnable' => null,
            'additional_information' => null,
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | SAVE LEAD TIME
    |--------------------------------------------------------------------------
    */

    if (array_key_exists('lead_time', $validated)) {
        $product->lead_time = $validated['lead_time'];
        $product->save();
    }

    return null;

    
}

    public function updateStock(Request $request, Product $product)
    {

        $this->authorize('update', $product);


        $request->validate([
            'stocks' => ['required', 'array'],
            'stocks.*' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $product) {

            foreach ($request->stocks as $warehouseId => $quantity) {

                ProductWarehouseStock::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouseId,
                    ],
                    [
                        'quantity' => (int) $quantity,
                    ]
                );
            }
        });

        // пересчёт общего стока (если нужно вернуть в UI)
        $totalStock = ProductWarehouseStock::where('product_id', $product->id)
            ->sum('quantity');

        return response()->json([
            'success' => true,
            'stock' => $totalStock,
        ]);
    }

    public function destroy(Product $product, DeleteProductAction $action)
    {

        $this->authorize('delete', $product);

        $action->execute($product);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }

    public function storeCustomAttribute(
        Request $request,

    ) {

        $this->authorize('create', Product::class);


        $owner = $this->context->supplier();
        $ownerType = $owner::class;

        $data = $request->validate([
            'id' => ['nullable', 'exists:attributes,id'],
            'key' => ['required', 'string'],
            'type' => ['required', 'string'],
            'options' => ['nullable', 'array'],
        ]);

        if ($request->filled('group_name')) {

            $group = AttributeGroup::firstOrCreate([
                'name' => $request->group_name,
                'owner_id' => $owner->id,
                'owner_type' => $ownerType,
                'created_by' => auth()->id(),
            ]);

            $groupId = $group->id;
        } else {
            $groupId = $request->group_id;
        }

        /*
    |--------------------------------------------------------------------------
    | CODE
    |--------------------------------------------------------------------------
    */
        $code = Str::slug($data['key'], '_');

        /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE (ONLY DEFINITION)
    |--------------------------------------------------------------------------
    */
        $attribute = Attribute::updateOrCreate(
            [
                'id' => $data['id'] ?? null,
                'entity_type' => 'product',
                'context' => 'product',
            ],
            [
                'code' => $code,
                'group_id' => $groupId ?? null,
                'type' => $data['type'],
                'is_custom' => 1,
                'is_system' => 0,
                'owner_type' => $ownerType,
                'owner_id' => $owner->id,
                'created_by' => auth()->id(),
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | TRANSLATION
    |--------------------------------------------------------------------------
    */
        $attribute->translations()->updateOrCreate(
            [
                'locale' => app()->getLocale(),
            ],
            [
                'name' => $data['key'],
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | OPTIONS (ONLY FOR SELECT TYPES)
    |--------------------------------------------------------------------------
    */
        if (in_array($data['type'], ['select', 'multiselect'])) {

            $attribute->options()->delete();

            foreach ($data['options'] ?? [] as $opt) {

                if (!$opt) continue;

                // ✔ СРАЗУ получаем созданный option
                $option = $attribute->options()->create([]);

                // ✔ НЕ create(), а updateOrCreate
                $option->translations()->updateOrCreate(
                    [
                        'locale' => app()->getLocale(),
                    ],
                    [
                        'value' => $opt,
                    ]
                );
            }
        }

        return back()->with('success', 'Attribute created');
    }

    public function attachAttributes(Request $request, Product $product)
    {

        $this->authorize('update', $product);

        $request->validate([
            'attributes' => ['array'],
            'attributes.*' => ['exists:attributes,id'],
        ]);

        $owner = $this->context->supplier();

        $allowedIds = Attribute::query()
            ->where('owner_type', $owner::class)
            ->where('owner_id', $owner->id)
            ->pluck('id');


        $attributeIds = collect($request->input('attributes', []))
            ->intersect($allowedIds)
            ->values();

        $customAttributeIds = Attribute::where('is_custom', 1)
            ->pluck('id');

        // attach missing
        foreach ($attributeIds as $attributeId) {
            ProductAttributeValue::firstOrCreate([
                'product_id' => $product->id,
                'attribute_id' => $attributeId,
            ]);
        }

        // optional sync (remove unchecked)
        ProductAttributeValue::where('product_id', $product->id)
            ->whereIn('attribute_id', $customAttributeIds)
            ->whereNotIn('attribute_id', $attributeIds)
            ->delete();

        return back();
    }

    public function deleteAttribute(
        Product $product,
        Attribute $attribute
    ) {
        Log::info('DELETE ATTRIBUTE START', [
            'product_id' => $product->id,
            'attribute_id' => $attribute->id,
        ]);

        $this->authorize('update', $product);

        $owner = $this->context->supplier();



        abort_unless(
            $attribute->is_custom &&
                $attribute->owner_type === $owner::class &&
                $attribute->owner_id == $owner->id,
            403
        );

        DB::transaction(function () use ($attribute) {



            $deleted = ProductAttributeValue::where('attribute_id', $attribute->id)
                ->delete();





            $updated = $attribute->update([
                'is_active' => 0,
            ]);
        });



        return response()->json([
            'success' => true,
        ]);
    }

    public function publish(
    Request $request,
    Product $product,
    UpdateProductCountryShippingAction $updateCountryShippingAction,
    ProductDTOFactory $dtoFactory
) {
    $this->authorize('update', $product);

    $response = $this->saveFinishStep(
        $request,
        $product,
        $updateCountryShippingAction,
        $dtoFactory
    );

    if ($response) {
        return $response;
    }

    $product->update([
        'status' => 'pending',
        'reject_reason' => null,
    ]);

    return redirect()
    ->route('supplier.products.index')
    ->with('success', 'Product submitted for moderation.');
}

public function saveDraft(
    Request $request,
    Product $product,
    UpdateProductCountryShippingAction $updateCountryShippingAction,
    ProductDTOFactory $dtoFactory
) {
    $this->authorize('update', $product);

    $response = $this->saveFinishStep(
        $request,
        $product,
        $updateCountryShippingAction,
        $dtoFactory
    );

    if ($response) {
        return $response;
    }

    return redirect()
    ->route('supplier.products.index')
    ->with('success', 'Product saved successfully.');
}


public function editApproved(
    Product $product,
    CreateProductEditingCopyAction $createEditingCopyAction
) {
    $this->authorize('update', $product);

    if ($product->status !== 'approved') {
        return redirect()->route(
            'supplier.products.edit-step',
            [$product->id, 1]
        );
    }

    $copy = $createEditingCopyAction->execute($product);

    return redirect()->route(
        'supplier.products.edit-step',
        [$copy->id, 1]
    );


}


public function archive(Product $product)
{
    $this->authorize('update', $product);

    if ($product->status === 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'Products pending moderation cannot be archived.',
        ], 422);
    }

    $product->update([
        'status' => 'archived',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Product archived successfully.',
    ]);
}

public function draftPreview(
    Product $product,
    ProductViewQueryService $service
) {
    $entity = $this->context->entity();

    abort_unless(
        (int) $product->supplier_id === (int) $entity->getKey()
        && $product->original_product_id !== null
        && in_array($product->status, ['draft', 'rejected']),
        404
    );

    $originalProduct = Product::query()
        ->whereKey($product->original_product_id)
        ->where('supplier_id', $entity->getKey())
        ->where('status', 'approved')
        ->firstOrFail();

    return view(
        'product.show',
        array_merge(
            $service->getProductViewData($product->slug),
            [
                'isDraftPreview' => true,
                'originalProduct' => $originalProduct,
            ]
        )
    );
}

public function bulkArchive(Request $request)
{
    $validated = $request->validate([
        'ids' => ['required', 'array', 'min:1'],
        'ids.*' => ['required', 'integer', 'distinct'],
    ]);

    $entity = $this->context->entity();
    $supplierId = $entity->getKey();
    $ids = $validated['ids'];

    $products = Product::query()
        ->where('supplier_id', $supplierId)
        ->whereIn('id', $ids)
        ->get();

    if ($products->count() !== count($ids)) {
        return response()->json([
            'success' => false,
            'message' => 'Some selected products were not found.',
        ], 422);
    }

    foreach ($products as $product) {
        $this->authorize('update', $product);

        $hasActiveCopy = Product::query()
            ->where('original_product_id', $product->id)
            ->whereIn('status', ['draft', 'rejected', 'pending'])
            ->exists();

        if ($product->status === 'pending' || $hasActiveCopy) {
            return response()->json([
                'success' => false,
                'message' => 'Some products cannot be archived because they are pending moderation or have an active editing copy.',
            ], 422);
        }
    }

    Product::query()
        ->where('supplier_id', $supplierId)
        ->whereIn('id', $ids)
        ->update(['status' => 'archived']);

    return response()->json([
        'success' => true,
        'message' => 'Selected products archived successfully.',
        'ids' => $ids,
    ]);
}



public function unpublish(Product $product)
{
    $entity = $this->context->entity();

    // Make sure the product belongs to the active supplier.
    abort_unless(
        (int) $product->supplier_id === (int) $entity->getKey(),
        404
    );

    $this->authorize('update', $product);

    // Only approved products can be unpublished.
    if ($product->status !== 'approved') {
        return response()->json([
            'success' => false,
            'message' => 'Only published products can be unpublished.',
        ], 422);
    }

    // Do not unpublish while an editing copy is active.
    $hasEditingCopy = Product::query()
        ->where('supplier_id', $entity->getKey())
        ->where('original_product_id', $product->id)
        ->whereIn('status', ['draft', 'pending', 'rejected'])
        ->exists();

    if ($hasEditingCopy) {
        return response()->json([
            'success' => false,
            'message' => 'This product cannot be unpublished while an editing copy exists.',
        ], 422);
    }

    $product->update([
        'status' => 'draft',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Product unpublished successfully.',
        'id' => $product->id,
    ]);
}


protected static function booted(): void
{
    // Сохрани существующие обработчики модели, если они уже есть.

    static::created(function (Product $product): void {
        // Editing copies do not get an independent list position.
        if (
            $product->original_product_id !== null
            || $product->status === 'archived'
        ) {
            return;
        }

        DB::transaction(function () use ($product): void {
            $otherIds = DB::table('products')
                ->where('supplier_id', $product->supplier_id)
                ->whereNull('original_product_id')
                ->where('status', '!=', 'archived')
                ->where('id', '!=', $product->id)
                ->lockForUpdate()
                ->pluck('id');

            if ($otherIds->isNotEmpty()) {
                DB::table('products')
                    ->whereIn('id', $otherIds)
                    ->increment('sort_order');
            }

            DB::table('products')
                ->where('id', $product->id)
                ->update(['sort_order' => 1]);
        });

        $product->sort_order = 1;
    });
}



public function moveToTop(Product $product)
{
    $entity = $this->context->entity();
    $supplierId = (int) $entity->getKey();

    abort_unless(
        (int) $product->supplier_id === $supplierId
            && $product->original_product_id === null,
        404
    );

    $this->authorize('update', $product);

    if ($product->status === 'archived') {
    return redirect()
        ->back()
        ->with('error', 'Archived products cannot be reordered.');
}

    DB::transaction(function () use ($product, $supplierId) {
        $ids = Product::query()
            ->where('supplier_id', $supplierId)
            ->whereNull('original_product_id')
            ->where('status', '!=', 'archived')
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (!in_array((int) $product->id, $ids, true)) {
            abort(404);
        }

        // Remove the selected product from its current position.
        $ids = array_values(array_filter(
            $ids,
            fn ($id) => $id !== (int) $product->id
        ));

        // Move it to the first position.
        array_unshift($ids, (int) $product->id);

        // Rewrite sequential positions: 1, 2, 3, ...
        foreach ($ids as $index => $id) {
            DB::table('products')
                ->where('id', $id)
                ->update([
                    'sort_order' => $index + 1,
                ]);
        }
    });

    return redirect()
    ->back()
    ->with('success', 'Product moved to the top of the list.');
}



public function duplicate(
    Product $product,
    DuplicateProductAction $action
) {
    $entity = $this->context->entity();

    abort_unless(
        (int) $product->supplier_id === (int) $entity->getKey()
            && $product->original_product_id === null,
        404
    );

    $this->authorize('update', $product);

    $copy = $action->execute(
        $product,
        (int) auth()->id()
    );

    return redirect()
        ->route('supplier.products.edit-step', [
            'product' => $copy->id,
            'step' => 1,
        ])
        ->with(
            'success',
            'Product copied successfully. Review the new draft before publishing.'
        );
}



}
