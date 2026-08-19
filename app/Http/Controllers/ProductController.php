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

/* === MODELS === */
use App\Models\Product;

use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\Warehouse;
use App\Models\ProductAttributeValue;
use App\Models\ProductWarehouseStock;
use App\Models\ProductVariantItem;
use App\Models\ProductVariantGroup;


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
        $products = $service->getSupplierProducts(
            $supplierId,
            $request->only(['sort', 'status', 'user'])
        );

        $warehouses = Warehouse::where('provider_id', $entity->getKey())
            ->where('provider_type', $entity::class)
            ->get();




        return view('dashboard.supplier.products', [
            'products' => $products,
            'warehouses' => $warehouses,
            'sort' => $request->sort,
            'status' => $request->status,
            'userFilter' => $request->user,
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

            $requiredAttributes = $product->category
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



            $updateProductMaterialsAction->execute(
                product: $product,
                materialsSelected: $request->materials_selected ?? '',

            );

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


            // 🔹 Сохраняем/обновляем Shipping Dimensions (габариты и вес упаковки)
            $shippingData = $request->input('shipping', []);

            if (!empty($shippingData)) {
                $product->shippingDimensions()->updateOrCreate(
                    [
                        'dimensionable_type' => Product::class,
                        'dimensionable_id'   => $product->id,
                    ], // Laravel автоматически подставит product_id
                    [
                        'length'       => $shippingData['length'] ?? 0,
                        'width'        => $shippingData['width'] ?? 0,
                        'height'       => $shippingData['height'] ?? 0,
                        'weight'       => $shippingData['weight'] ?? 0,
                        'package_type' => $shippingData['package_type'] ?? 'box',
                    ]
                );
            }
            $dto = $dtoFactory->fromUpdateCountryRequest($request);

            $updateCountryShippingaction->execute(
                product: $product,
                data: $dto,
                shippingTemplates: $request->shipping_templates ?? [],

            );


            return redirect()
                ->route('supplier.products.edit-step', [
                    'product' => $product->id,
                    'step' => $nextstep,
                ]);
        } elseif ($step == 7) {





            if ($request->has('variants')) {



                // 🔹 Новый вариант — ищем variant_group_id среди существующих айтемов
                $variantGroupId = $product->variantItems()->first()?->variant_group_id;

                // Если группы нет и будут добавляться новые айтемы, создаём её
                if (!$variantGroupId && collect($request->variants)->filter(fn($v) => !empty($v['linked_product_id']))->isNotEmpty()) {
                    $variantGroup = ProductVariantGroup::create([
                        'product_id' => $product->id,
                    ]);
                    $variantGroupId = $variantGroup->id;

                    // Обновляем родительский продукт
                    $product->update(['variant_group_id' => $variantGroupId]);
                }

                $incomingIds = collect($request->variants)->pluck('id')->filter()->all();
                $existingVariants = $product->variantItems;

                // Удаляем отсутствующие
                $existingVariants->each(function ($variant) use ($incomingIds) {
                    if (!in_array($variant->id, $incomingIds)) {
                        if ($variant->media) $this->mediaService->delete($variant->media);
                        $product = $variant->product;
                        $variant->delete();

                        $product->update(['variant_group_id' => null]);
                    }
                });







                // Создаем / обновляем
                foreach ($request->variants as $variantData) {

                    if (!empty($variantData['id'])) {
                        // 🔹 Существующий вариант

                        $variant = ProductVariantItem::find($variantData['id']);


                        //БЕЗОПАСНІЙ ВАРИАНТ. ПОТОМ ПОМЕНЯТЬ И ПРОВЕРИТЬ
                        // $variant = ProductVariantItem::where('variant_group_id', $product->variant_group_id)->find($variantData['id']);



                        if (!$variant) continue;

                        $variant->title = $variantData['title'];
                        $variant->product_id = $variantData['linked_product_id'] ?? null;
                        $variant->save();
                    } else {




                        // 🔹 Создаём новый вариант
                        $variant = ProductVariantItem::create([
                            'product_id' => $variantData['linked_product_id'] ?? null,
                            'variant_group_id' => $variantGroupId,
                            'title' => $variantData['title'],

                        ]);

                        $linkedProductId = $variantData['linked_product_id'] ?? $product->id;
                        if ($linkedProductId) {
                            Product::where('id', $linkedProductId)
                                ->update(['variant_group_id' => $variantGroupId]);
                        }
                    }

                    // 🔹 Обработка изображения
                    if (!empty($variantData['image'])) {

                        $media = $this->mediaService->upload(
                            new UploadMediaDTO(
                                file: $variantData['image'],
                                model: $variant,
                                collection: 'product_variant_image',
                                sortOrder: 0,
                                isMain: false
                            )
                        );

                        $variant->update(['media_id' => $media->id]);
                    }
                }

                // Проверяем, сколько айтемов осталось в группе
                $remainingVariants = $product->variantItems()->get();

                if ($remainingVariants->count() <= 1) {
                    foreach ($remainingVariants as $variant) {
                        if ($variant->media) $this->mediaService->delete($variant->media);
                        $variant->delete();
                    }

                    // Удаляем саму группу
                    if ($product->variant_group_id) {
                        ProductVariantGroup::find($product->variant_group_id)?->delete();
                        $product->update(['variant_group_id' => null]);
                    }
                }
            }




            $dto = $dtoFactory->fromUpdateVariantRequest($request);


            $updateProductVariantAction->execute(
                product: $product,
                data: $dto,

            );
        }

        return redirect()
            ->route('supplier.products.edit-step', [
                'product' => $product->id,
                'step' => $step,
            ]);
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
}
