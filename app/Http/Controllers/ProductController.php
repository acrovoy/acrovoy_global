<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


/* === REQUEST === */
use App\Http\Requests\UpdateProductRequest;

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
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use App\Models\Language;
use App\Models\MessageThread;
use App\Models\Project;


use App\Domain\Product\Actions\CreateProductAction;
use App\Domain\Product\Actions\SyncProductTranslationAction;
use App\Domain\Product\Actions\SyncProductMediaAction;
use App\Domain\Product\Actions\SyncProductPriceTierAction;
use App\Domain\Product\Actions\SyncProductSpecificationAction;
use App\Domain\Product\Actions\SyncProductMaterialAction;


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

        return view('dashboard.manufacturer.add-product', $data);
    }


    public function store(StoreProductRequest $request, CreateProductAction $createProduct, SyncProductTranslationAction $translationAction, SyncProductMediaAction $mediaAction,
    SyncProductPriceTierAction $priceAction, SyncProductSpecificationAction $specAction, SyncProductMaterialAction $materialAction
    ) {

        DB::transaction(function () use (
            $request,
            $createProduct,
            $translationAction,
            $priceAction,
            $specAction,
            $materialAction
        ) {
            $product = $createProduct->execute(ProductDTO::fromRequest($request));

            $translationAction->execute(
                $product,
                $request->name,
                $request->undername,
                $request->description
            );

            $mediaService = app(MediaService::class);

           
            $files = $request->file('images', []);
            \Log::info('FILES DEBUG', [
                'has_images' => $request->hasFile('images'),
                'files_raw' => $request->file('images'),
                'all_request' => $request->all()
            ]);

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
        });

        return redirect()->route('manufacturer.products.index')
            ->with('success', 'Product created successfully');
    }


    public function edit(Product $product, ProductEditQueryService $service)
    {
        return view(
            'product.edit',
            $service->getEditViewData($product)
        );
    }

    public function update(UpdateProductRequest $request, Product $product, UpdateProductAction $action) 
    {

        abort_if(
            $product->supplier_id !== auth()->user()->supplier->id,
            403
        );

        $dto = ProductDTO::fromUpdateRequest($request);

       $translations = [];

        foreach ($request->name as $locale => $name) {
            $translations[$locale] = [
                'name' => $name,
                'undername' => $request->undername[$locale] ?? null,
                'description' => $request->description[$locale] ?? null,
            ];
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
        specifications: $request->specs ?? []
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
