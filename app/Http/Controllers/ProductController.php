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


class ProductController extends Controller
{

    public function index(Request $request, ProductListQueryService $service)
    {
        $products = $service->getSupplierProducts(
            Auth::user()->supplier->id,
            $request->only(['sort', 'status', 'user'])
        );

        return view('dashboard.manufacturer.products', [
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

    public function create(ProductFormDataService $service)
    {

        $data = $service->getCreateFormData();
        $products = Product::with('translations') // Загружаем сразу переводы
            ->where('supplier_id', Auth::user()->supplier->id)
            ->get();


       




        return view('dashboard.manufacturer.add-product', array_merge($data, [
            'products' => $products
        ]));
    }


    public function store(
        StoreProductRequest $request,
        CreateProductAction $createProduct,
        SyncProductTranslationAction $translationAction,
        SyncProductPriceTierAction $priceAction,
        SyncProductAttributeAction $attributeAction,
        SyncProductSpecificationAction $specAction,
        SyncProductMaterialAction $materialAction,
        SyncShippingTemplateAction $shippingAction,
        AttachProductVariantAction $attachProductVariantAction,
        ProductDTOFactory $dtoFactory,
    ) {


   


        DB::transaction(function () use (
            $request,
            $createProduct,
            $translationAction,
            $priceAction,
            $specAction,
            $materialAction,
            $shippingAction,
            $dtoFactory,
            $attachProductVariantAction,
            $attributeAction,
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
                    if (!$variantProduct || $variantProduct->supplier_id !== $product->supplier_id) continue;

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

            $specAction->execute($product, $request->specs ?? []);

            $shippingAction->execute(
                $product,
                $request->shipping_templates ?? []
            );

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

        return redirect()->route('manufacturer.products.index')
            ->with('success', 'Product created successfully');
    }


    public function edit(Product $product, ProductEditQueryService $service)
    {

        $products = Product::with('translations', 'shippingDimensions') // Загружаем сразу переводы
            ->where('supplier_id', Auth::user()->supplier->id)
            ->get();

        return view(
            'product.edit',
            $service->getEditViewData($product)
        );
    }

    public function update(UpdateProductRequest $request, Product $product, UpdateProductAction $action, ProductDTOFactory $dtoFactory, SyncProductAttributeAction $attributeAction,)
    {



        abort_if(
            $product->supplier_id !== auth()->user()->supplier->id,
            403
        );

        $dto = $dtoFactory->fromUpdateRequest($request);

        $translations = [];

        foreach ($request->name as $locale => $name) {
            $translations[$locale] = [
                'name' => $name,
                'undername' => $request->undername[$locale] ?? null,
                'description' => $request->description[$locale] ?? null,
            ];
        }



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



$attributes = $request->input('attributes', []); // Если атрибутов нет, массив пустой
if ($attributes instanceof \Symfony\Component\HttpFoundation\ParameterBag) {
    $attributes = $attributes->all();
}



        $action->execute(
            product: $product,
            data: $dto,
            translations: $translations,
            shippingTemplates: $request->shipping_templates ?? [],
            mediaFiles: $request->file('images', []),
            existingIds: $request->existing_ids ?? [],
            sortOrder: $request->sort_order ?? [],
            existingSortOrder: $request->existing_sort_order ?? [],
            isMain: $request->is_main ?? [],
            priceTiers: $request->price_tiers ?? [],
            materialsSelected: $request->materials_selected ?? '',
            specifications: $request->specs ?? [],
            attributes:  $attributes,
        );

        

        return redirect()
            ->route('manufacturer.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        // Проверка, что товар принадлежит supplier'у
        if ($product->supplier_id !== auth()->user()->supplier->id) {
            abort(403);
        }

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
}
