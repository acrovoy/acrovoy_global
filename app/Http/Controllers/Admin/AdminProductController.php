<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Domain\Product\Actions\ApplyProductEditingCopyAction;
use App\Domain\Product\Actions\DeleteProductEditingCopyAction;
use App\Domain\Returns\Models\ProductReturnPolicy;
use Illuminate\Validation\ValidationException;
use App\Domain\Product\Actions\DeleteProductAction;

use App\Models\User;
use App\Models\Country;
use App\Models\Language;
use App\Models\Location;



class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'newest');
        $supplierFilter = $request->get('supplier');

        $query = Product::query()
            ->with([
                'images',
                'supplier',
                'category',
                'priceTiers',
            ])
            ->where('status', 'pending');

        if ($supplierFilter) {
            $query->whereHas('supplier', function ($q) use ($supplierFilter) {
                $q->where('name', 'like', "%{$supplierFilter}%");
            });
        }

        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(20)->withQueryString();

        
        return view('dashboard.admin.products.index', compact(
            'products',
            'sort',
            'supplierFilter'
        ));
    }

    public function show(Product $product)
{
    $product->load([
        'images',
        'priceTiers',
        'supplier',
        'category.translations',
        'user',
        'translations',
        'materials.translations',
        'shippingDimensions',
        'shippingTemplates.translations',
        'shippingTemplates.warehouse',
        'shippingTemplates.locations',
        'shippingTemplates.deliveryOptions.deliveryType',
        'paymentMethods.translations',
        'paymentTerms.translations',
        'attributeValues.attribute.translations',
        'attributeValues.attribute.group',
        'attributeValues.translations',
        'attributeValues.options.option.translations',
        'variantGroup.items.product.translations',
        'variantGroup.items.media',
        'variantPreview',
    ]);

    $images = $product->images
        ->sortBy('sort_order')
        ->values();

    $mainImage = $images->firstWhere('is_main', true)
        ?? $images->firstWhere('is_primary', true)
        ?? $images->first();

    $creator = $product->created_by
        ? User::find($product->created_by)
        : null;

    $supplierLanguage = $product->supplier?->language
        ? Language::where('code', $product->supplier->language)->first()
        : null;

    $originCountry = $product->country_id
        ? Country::withCurrentTranslation()->whereKey($product->country_id)->first()
        : null;

    $originRegion = $product->origin_region_id
        ? Location::find($product->origin_region_id)
        : null;

    $originCity = $product->origin_city_id
        ? Location::find($product->origin_city_id)
        : null;

    $productReturnPolicy = ProductReturnPolicy::query()
        ->with([
            'returnPolicy.translations',
            'returnPolicy.reasons.translations',
            'returnPolicy.resolutions.translations',
        ])
        ->where('product_id', $product->id)
        ->first();

    $originalProduct = $product->original_product_id
        ? Product::with('translations')->find($product->original_product_id)
        : null;

    return view('dashboard.admin.products.show', [
        'product1' => $product,
        'images' => $images,
        'mainImage' => $mainImage,
        'creator' => $creator,
        'supplierLanguage' => $supplierLanguage,
        'originCountry' => $originCountry,
        'originRegion' => $originRegion,
        'originCity' => $originCity,
        'productReturnPolicy' => $productReturnPolicy,
        'originalProduct' => $originalProduct,
    ]);
}

    public function approve(
    Product $product,
    ApplyProductEditingCopyAction $applyEditingCopyAction,
    DeleteProductEditingCopyAction $deleteEditingCopyAction
) {
    try {
        if ($product->original_product_id !== null) {
            $original = $applyEditingCopyAction->execute($product);

            $deleteEditingCopyAction->execute($product);

            return redirect()
                ->route('admin.products.index')
                ->with(
                    'success',
                    "Changes to product #{$original->id} approved successfully."
                );
        }

        if ($product->status !== 'pending') {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Only pending products can be approved.');
        }

        $product->update([
            'status' => 'approved',
            'reject_reason' => null,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                "Product #{$product->id} approved successfully."
            );
    } catch (ValidationException $e) {
        throw $e;
    }
}

    public function reject(Request $request, Product $product)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        $product->update([
            'status' => 'rejected',
            'reject_reason' => $request->input('reject_reason'),
        ]);

        return back()->with('error', 'Product rejected with reason.');
    }


    public function destroy(
    Product $product,
    DeleteProductAction $deleteProductAction
) {
    try {
        $productId = $product->id;

        $deleteProductAction->execute($product);

        return redirect()
            ->route('admin.products.management')
            ->with(
                'success',
                "Product #{$productId} deleted successfully."
            );
    } catch (ValidationException $e) {
        throw $e;
    }
}

public function managementIndex()
{
    $sort = request('sort', '');
    $status = request('status', '');
    $userFilter = request('user', '');

    $products = Product::query()
        ->with(['supplier.user', 'images'])
        ->whereNull('original_product_id')
        ->when(
            in_array($status, ['pending', 'approved', 'rejected'], true),
            fn ($query) => $query->where('status', $status)
        )
        ->when(
            $userFilter !== '',
            fn ($query) => $query->whereHas('supplier.user', function ($query) use ($userFilter) {
                $query->where('name', 'like', "%{$userFilter}%")
                    ->orWhere('last_name', 'like', "%{$userFilter}%");
            })
        )
        ->when(
            $sort === 'oldest',
            fn ($query) => $query->orderBy('created_at'),
            fn ($query) => $query->orderByDesc('created_at')
        )
        ->paginate(20)
        ->withQueryString();

    return view('dashboard.admin.products.management', compact(
        'products',
        'sort',
        'status',
        'userFilter'
    ));
}

}