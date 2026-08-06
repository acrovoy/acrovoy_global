<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Domain\Collection\Services\ProductCollectionService;
use App\Http\Controllers\Controller;

use App\Domain\Collection\Models\ProductCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use App\Models\Product;

class ProductCollectionController extends Controller
{
    public function __construct(
        protected ProductCollectionService $collections
    ) {
    }

    /**
     * Collections list
     */
    public function index(): View
    {
        $collections = ProductCollection::query()
            ->with([
                'translations',               
                'owner',
                'cover',
            ])
            ->withCount('products')
            ->latest()
            ->paginate(20);

        return view(
            'dashboard.admin.collections.index',
            compact('collections')
        );
    }

    /**
     * Create form
     */
    public function create(): View
    {
        return view(
            'dashboard.admin.collections.create'
        );
    }

    /**
     * Store
     */
    public function store(
        Request $request
    ): RedirectResponse {

        $collection = $this->collections->create(
            $request->all()
        );

        return redirect()
            ->route(
                'admin.collections.edit',
                $collection
            )
            ->with(
                'success',
                __('Collection created successfully.')
            );
    }

    /**
     * Show
     */
    public function show(
        ProductCollection $collection
    ): View {

        $collection->load([
            'translations',
            'products',
            'media',
        ]);

        return view(
            'dashboard.admin.collections.show',
            compact('collection')
        );
    }

    /**
     * Edit form
     */
    public function edit(
        ProductCollection $collection
    ): View {

        $collection->load([
            'translations',
            'products',
            'media',
        ]);

        return view(
            'dashboard.admin.collections.edit',
            compact('collection')
        );
    }

    /**
     * Update
     */
    public function update(
        Request $request,
        ProductCollection $collection
    ): RedirectResponse {

        $this->collections->update(
            $collection,
            $request->all()
        );

        return back()->with(
            'success',
            __('Collection updated successfully.')
        );
    }

    /**
     * Delete
     */
    public function destroy(
        ProductCollection $collection
    ): RedirectResponse {

        $this->collections->delete(
            $collection
        );

        return redirect()
            ->route('admin.collections.index')
            ->with(
                'success',
                __('Collection deleted successfully.')
            );
    }

    public function searchProducts(
    Request $request,
    ProductCollection $collection
): JsonResponse {

    




    $query = trim($request->q);

    if (mb_strlen($query) < 2) {
        return response()->json([]);
    }

    $products = Product::query()

        ->with([
            'translations',
            'images',
        ])

        ->whereNotIn(
            'id',
            $collection->products()->pluck('products.id')
        )

        ->where(function ($q) use ($query) {

            $q->where('sku', 'like', "%{$query}%")
              ->orWhere('slug', 'like', "%{$query}%")
              ->orWhereHas('translations', function ($q) use ($query) {

                    $q->where('name', 'like', "%{$query}%");

              });

        })

        ->limit(20)

        ->get();

    return response()->json(
    $products->map(function ($product) {

        return [
            'id' => $product->id,

            'name' => $product->name,

            'sku' => $product->sku,

            'image' => $product->main_image_url
    ? asset($product->main_image_url)
    : asset('images/no-image.png'),

        ];

    })
);

}

public function attachProducts(
    Request $request,
    ProductCollection $collection
): JsonResponse {



    $request->validate([
        'product_id' => [
            'required',
            'exists:products,id',
        ],
    ]);


    $collection->products()->syncWithoutDetaching([

        $request->product_id => [
            'sort_order' => $collection->products()->count(),
        ]

    ]);


    return response()->json([

        'success' => true,

        'message' => 'Product added to collection.',

    ]);

}


public function detachProduct(
    ProductCollection $collection,
    Product $product
): JsonResponse {

    $collection->products()->detach($product->id);

    return response()->json([
        'success' => true
    ]);
}



public function products(ProductCollection $collection): JsonResponse
{
    $products = $collection->products()
    ->with([
        'translations',
        'images',
    ])
    ->orderByPivot('sort_order')
    ->get();


    return response()->json(
        $products->map(function ($product) {

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'image' => $product->main_image_url,
            ];

        })
    );
}

public function reorder(
    Request $request,
    ProductCollection $collection
): JsonResponse {

    $request->validate([
    'products' => ['required', 'array'],
    'products.*.id' => ['required', 'integer'],
    'products.*.sort_order' => ['required', 'integer'],
]);


    foreach ($request->products as $product) {

    $collection->products()->updateExistingPivot(
        $product['id'],
        [
            'sort_order' => $product['sort_order']
        ]
    );

}


    return response()->json([
        'success' => true
    ]);
}

}