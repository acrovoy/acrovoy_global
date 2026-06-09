<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Domain\Product\Factories\ProductDTOFactory;

/* === REQUEST === */
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\StoreProductRequest;

/* === SERVICES === */
use App\Domain\Media\Services\MediaService;
use App\Domain\Media\DTO\UploadMediaDTO;
use App\Domain\Product\Services\ProductFormDataService;
use App\Domain\Product\Services\ProductEditQueryService;
use App\Domain\Product\Services\ProductViewQueryService;
use App\Domain\Product\Services\ProductListQueryService;

/* === ACTIONS === */
use App\Domain\Product\Actions\DeleteProductAction;
use App\Domain\Product\Actions\UpdateProductAction;
use App\Domain\Product\Actions\UpdateProductBasicInfoAction;
use App\Domain\Product\Actions\UpdateProductCategoryAction;
use App\Domain\Product\Actions\UpdateProductMaterialsAction;
use App\Domain\Product\Actions\UpdateProductMediaAction;
use App\Domain\Product\Actions\UpdateProductMoqPriceAction;
use App\Domain\Product\Actions\UpdateProductCountryShippingAction;
use App\Domain\Product\Actions\UpdateProductVariantAction;
use App\Domain\Product\Actions\AttachProductVariantAction;

/* === DTO === */
use App\Domain\Product\DTO\ProductDTO;

/* === MODELS === */
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductPriceTier;
use App\Models\ProductMaterial;
use App\Models\Material;
use App\Models\Specification;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Color;
use App\Models\ShippingTemplate;
use App\Models\ProductVariantGroup;
use App\Models\ProductVariantItem;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\ProductAttributeValue;

use App\Models\Country;
use App\Models\Language;
use App\Models\MessageThread;
use App\Models\Project;

use App\Domain\Product\Actions\CreateProductAction;
use App\Domain\Product\Actions\SyncProductTranslationAction;

use App\Domain\Product\Actions\SyncProductPriceTierAction;
use App\Domain\Product\Actions\SyncProductSpecificationAction;
use App\Domain\Product\Actions\SyncProductAttributeAction;
use App\Domain\Product\Actions\SyncProductMaterialAction;
use App\Domain\Product\Actions\SyncShippingTemplateAction;
use App\Domain\Product\Actions\SyncProductCustomAttributeAction;

use App\Services\Company\ActiveContextService;


class ProductController extends Controller
{

    protected ActiveContextService $activeContext;

    public function __construct(ActiveContextService $activeContext)
    {
        $this->activeContext = $activeContext;
    }

    public function index(Request $request, ProductListQueryService $service)
    {
        $products = $service->getSupplierProducts(
            $this->activeContext->id(),
            $this->activeContext->type(),
            $request->only(['sort', 'status', 'user'])
        );

        return view('dashboard.supplier.products', [
            'products' => $products,
            'sort' => $request->sort,
            'status' => $request->status,
            'userFilter' => $request->user,
        ]);
    }


    public function show(string $slug, ProductViewQueryService $service)
    {
        return view('product.show', $service->getProductViewData($slug));
    }


    public function createNew(ProductFormDataService $service)
    {



        $supplierId = $this->activeContext->id();
        $supplierType = $this->activeContext->type();
        $products = Product::with('translations')
            ->where('supplier_id', $supplierId)
            ->where('supplier_type', $supplierType)
            ->get();
        $availableAttributesGrouped = collect();

        $data = $service->getCreateFormData();

        return view('product.create.add-product', array_merge($data, [
            'steps' => 1,
            'countries' => Country::all(),
            'products' => $products,
            'availableAttributesGrouped' => $availableAttributesGrouped,
        ]));
    }


    public function createStep2(ProductFormDataService $service)
    {



        $supplierId = $this->activeContext->id();
        $supplierType = $this->activeContext->type();
        $products = Product::with('translations')
            ->where('supplier_id', $supplierId)
            ->where('supplier_type', $supplierType)
            ->get();
        $availableAttributesGrouped = collect();

        $data = $service->getCreateFormData();

        return view('product.create.add-product', array_merge($data, [
            'steps' => 2,
            'countries' => Country::all(),
            'products' => $products,
            'availableAttributesGrouped' => $availableAttributesGrouped,
        ]));
    }


    public function create(ProductFormDataService $service)
    {



        $supplierId = $this->activeContext->id();
        $supplierType = $this->activeContext->type();




        $data = $service->getCreateFormData();





        $products = Product::with('translations')
            ->where('supplier_id', $supplierId)
            ->where('supplier_type', $supplierType)
            ->get();


        $ownerType = $this->activeContext->isPersonal()
            ? \App\Models\User::class
            : \App\Models\Supplier::class;

        $ownerId = $this->activeContext->id();




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




        return view('dashboard.supplier.add-product', array_merge($data, [
            'products' => $products,
            'groups' => $groups,
            'availableAttributesGrouped' => $availableAttributesGrouped,
            'availableAttributes' => $availableAttributes,

        ]));
    }


    public function storeStep1(
        Request $request,
        CreateProductAction $createProduct,
        SyncProductTranslationAction $translationAction,
        SyncProductPriceTierAction $priceAction,
        SyncProductAttributeAction $attributeAction,
        SyncProductCustomAttributeAction $customAttributeAction,
        SyncProductMaterialAction $materialAction,
        SyncShippingTemplateAction $shippingAction,
        AttachProductVariantAction $attachProductVariantAction,
        ProductDTOFactory $dtoFactory,
    ) {

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


    


    public function store(
        StoreProductRequest $request,
        CreateProductAction $createProduct,
        SyncProductTranslationAction $translationAction,
        SyncProductPriceTierAction $priceAction,
        SyncProductAttributeAction $attributeAction,
        SyncProductCustomAttributeAction $customAttributeAction,
        SyncProductMaterialAction $materialAction,
        SyncShippingTemplateAction $shippingAction,
        AttachProductVariantAction $attachProductVariantAction,
        ProductDTOFactory $dtoFactory,
    ) {





        $supplierId = $this->activeContext->id();
        $supplierType = $this->activeContext->type();


        DB::transaction(function () use (
            $request,
            $createProduct,
            $translationAction,
            $priceAction,
            $materialAction,
            $shippingAction,
            $dtoFactory,
            $attachProductVariantAction,
            $attributeAction,
            $customAttributeAction,
            $supplierId,
            $supplierType,
        ) {

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


            /*
        |-------------------------------------------------------------------------- 
        | Variant Group Guarantee
        |-------------------------------------------------------------------------- 
        */

            $variantProducts = $request->input('variant_products', []);
            $variantTitles = $request->input('variant_titles', []);
            $variantImages = $request->file('variant_images', []);

            $parentTitle = $request->input('parent_title', $product->name);
            $parentFile = $request->file('parent_image');

            $mediaService = app(\App\Domain\Media\Services\MediaService::class);

            // 🔹 Создаём родительский вариант только если есть что-то для группы
            if (!empty($parentTitle) || count($variantProducts) > 0) {

                // 🔹 Создаём или берём группу
                $group = $product->variant_group_id
                    ? ProductVariantGroup::findOrFail($product->variant_group_id)
                    : ProductVariantGroup::create([
                        'name' => $parentTitle,
                        'variant_hash' => \Illuminate\Support\Str::uuid()->toString(),
                    ]);

                if (!$product->variant_group_id) {
                    $product->update(['variant_group_id' => $group->id]);
                }

                // 🔹 Создаём ProductVariantItem для родителя
                $parentVariantItem = \App\Models\ProductVariantItem::updateOrCreate(
                    [
                        'variant_group_id' => $group->id,
                        'product_id' => $product->id,
                    ],
                    ['title' => $parentTitle]
                );

                // 🔹 Загружаем media для родителя
                if ($parentFile) {
                    $dto = new \App\Domain\Media\DTO\UploadMediaDTO(
                        file: $parentFile,
                        model: $product,
                        collection: 'product_variant_image',
                        mediaRole: 'variant_image',
                        private: false,
                        originalFileName: $parentFile->getClientOriginalName(),
                        metadata: [],
                        sortOrder: 0,
                        isMain: true
                    );
                    $uploadedMedia = $mediaService->upload($dto);
                    $parentVariantItem->update(['media_id' => $uploadedMedia->id]);
                }

                // 🔹 Обрабатываем остальные варианты
                foreach ($variantProducts as $index => $variantProductId) {
                    if (empty($variantProductId)) continue;

                    $variantProduct = Product::find($variantProductId);
                    if (
                        !$variantProduct ||
                        $variantProduct->supplier_id !== $supplierId ||
                        $variantProduct->supplierType !== $supplierType
                    ) continue;

                    $group = $attachProductVariantAction->execute($product, $variantProduct);

                    // 🔹 Обновляем variant_group_id у привязанного продукта
                    if ($variantProduct->variant_group_id !== $group->id) {
                        $variantProduct->update(['variant_group_id' => $group->id]);
                    }


                    $title = $variantTitles[$index] ?? $variantProduct->name;
                    $variantItem = \App\Models\ProductVariantItem::updateOrCreate(
                        [
                            'variant_group_id' => $group->id,
                            'product_id' => $variantProduct->id,
                        ],
                        ['title' => $title]
                    );

                    $file = $variantImages[$index] ?? null;
                    if ($file) {
                        $dto = new \App\Domain\Media\DTO\UploadMediaDTO(
                            file: $file,
                            model: $variantProduct,
                            collection: 'product_variant_image',
                            mediaRole: 'variant_image',
                            private: false,
                            originalFileName: $file->getClientOriginalName(),
                            metadata: [],
                            sortOrder: $index,
                            isMain: true
                        );
                        $uploadedMedia = $mediaService->upload($dto);
                        $variantItem->update(['media_id' => $uploadedMedia->id]);
                    }
                }
            }

            /*
        |-------------------------------------------------------------------------- 
        | Other Syncs
        |-------------------------------------------------------------------------- 
        */
            $files = $request->file('images', []);

            foreach ($files as $index => $file) {
                $dto = new UploadMediaDTO(
                    file: $file,
                    model: $product,
                    collection: 'product_gallery',
                    mediaRole: 'product_image',
                    private: false,
                    originalFileName: $file->getClientOriginalName(),
                    metadata: [],
                    sortOrder: $request->sort_order[$index] ?? $index,
                    isMain: ($request->is_main[$index] ?? 0) == 1
                );
                $mediaService->upload($dto);
            }

            $priceAction->execute($product, $request->price_tiers ?? []);

            $materialIds = explode(',', $request->materials_selected ?? '');
            $materialAction->execute($product, array_filter($materialIds));

            

            $shippingAction->execute(
                $product,
                $request->shipping_templates ?? []
            );


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





            // 🔹 **Синхронизация атрибутов продукта**
            if ($request->has('attributes')) {
                $attributeAction->execute($product, $request->input('attributes'));
            }


            // 🔹 Сохраняем Shipping Dimensions (габариты и вес упаковки)
            $shippingData = $request->input('shipping', []);

            if (!empty($shippingData)) {
                $product->shippingDimensions()->updateOrCreate(
                    [], // Laravel автоматически подставит product_id
                    [
                        'length' => $shippingData['length'] ?? 0,
                        'width'  => $shippingData['width'] ?? 0,
                        'height' => $shippingData['height'] ?? 0,
                        'weight' => $shippingData['weight'] ?? 0,
                        'package_type' => $shippingData['package_type'] ?? 'box',
                    ]
                );
            }
        });

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product created successfully');
    }


    public function edit(
        Product $product,
        ProductEditQueryService $service,

    ) {






        return view(
            'product.edit',
            $service->getEditViewData($product)
        );
    }

    public function editStep(
        Product $product,
        ProductEditQueryService $service,
        $step = 1
    ) {

        $ownerType = $this->activeContext->isPersonal()
            ? \App\Models\User::class
            : \App\Models\Supplier::class;

        $ownerId = $this->activeContext->id();




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

   

    public function updateStep(
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
        SyncProductAttributeAction $attributeAction,
        $step = 1
    ) {


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
                    [], // Laravel автоматически подставит product_id
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

                $mediaService = app(\App\Domain\Media\Services\MediaService::class);

                // 🔹 Новый вариант — ищем variant_group_id среди существующих айтемов
                $variantGroupId = $product->variantItems()->first()?->variant_group_id;

                // Если группы нет и будут добавляться новые айтемы, создаём её
                if (!$variantGroupId && collect($request->variants)->filter(fn($v) => !empty($v['linked_product_id']))->isNotEmpty()) {
                    $variantGroup = \App\Models\ProductVariantGroup::create([
                        'product_id' => $product->id,
                    ]);
                    $variantGroupId = $variantGroup->id;

                    // Обновляем родительский продукт
                    $product->update(['variant_group_id' => $variantGroupId]);
                }

                $incomingIds = collect($request->variants)->pluck('id')->filter()->all();
                $existingVariants = $product->variantItems;

                // Удаляем отсутствующие
                $existingVariants->each(function ($variant) use ($incomingIds, $mediaService) {
                    if (!in_array($variant->id, $incomingIds)) {
                        if ($variant->media) $mediaService->delete($variant->media);
                        $product = $variant->product;
                        $variant->delete();

                        $product->update(['variant_group_id' => null]);
                    }
                });







                // Создаем / обновляем
                foreach ($request->variants as $variantData) {

                    if (!empty($variantData['id'])) {
                        // 🔹 Существующий вариант

                        $variant = \App\Models\ProductVariantItem::find($variantData['id']);


                        //БЕЗОПАСНІЙ ВАРИАНТ. ПОТОМ ПОМЕНЯТЬ И ПРОВЕРИТЬ
                        // $variant = ProductVariantItem::where('variant_group_id', $product->variant_group_id)->find($variantData['id']);



                        if (!$variant) continue;

                        $variant->title = $variantData['title'];
                        $variant->product_id = $variantData['linked_product_id'] ?? null;
                        $variant->save();
                    } else {




                        // 🔹 Создаём новый вариант
                        $variant = \App\Models\ProductVariantItem::create([
                            'product_id' => $variantData['linked_product_id'] ?? null,
                            'variant_group_id' => $variantGroupId,
                            'title' => $variantData['title'],

                        ]);

                        $linkedProductId = $variantData['linked_product_id'] ?? $product->id;
                        if ($linkedProductId) {
                            \App\Models\Product::where('id', $linkedProductId)
                                ->update(['variant_group_id' => $variantGroupId]);
                        }
                    }

                    // 🔹 Обработка изображения
                    if (!empty($variantData['image'])) {
                        $mediaService = app(\App\Domain\Media\Services\MediaService::class);
                        $media = $mediaService->upload(
                            new \App\Domain\Media\DTO\UploadMediaDTO(
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
                        if ($variant->media) $mediaService->delete($variant->media);
                        $variant->delete();
                    }

                    // Удаляем саму группу
                    if ($product->variant_group_id) {
                        \App\Models\ProductVariantGroup::find($product->variant_group_id)?->delete();
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


    public function updateStock(
        Request $request,
        Product $product,

    ) {

        $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $ActiveContext = $this->activeContext;



        abort_if($product->supplier_id !== $ActiveContext->id(), 403);

        $product->stock()->update([
            'quantity' => $request->stock
        ]);

        return response()->json([
            'success' => true,
            'stock' => $request->stock,
        ]);
    }

    public function destroy(Product $product, DeleteProductAction $action)
    {
        $action->execute($product);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }

    public function storeCustomAttribute(
        Request $request,

        ActiveContextService $context
    ) {
        $ownerType = $context->isPersonal()
            ? 'App\Models\User'
            : 'App\Models\Supplier';

        $owner = $context->isPersonal()
            ? auth()->user()
            : $context->company();

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
        $request->validate([
            'attributes' => ['array'],
            'attributes.*' => ['exists:attributes,id'],
        ]);

        $attributeIds = $request->input('attributes', []);

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
}
