<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Filters\SupplierFilter;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Country;
use App\Models\ExportMarket;
use App\Models\Product;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        // Categories (3 уровня)
        $categories = Category::with(['children.children'])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        // Countries
        $countries = Country::withCurrentTranslation()
            ->orderBy('name')->get();

        // Supplier Types
        $types = [
            'trusted'  => 'Trusted',
            'verified' => 'Verified',
        ];

        // Active Filters
        $activeCategory = $request->get('category');
        $activeCountries = collect($request->get('country', []))
            ->map(fn($id) => (int)$id)
            ->toArray();
        $activeTypes = collect($request->get('supplier_type', []))->toArray();
        $exportMarkets = ExportMarket::with('translation')->get();
        $activeExportMarkets = collect($request->get('export_market', []))
            ->map(fn($id) => (int)$id)
            ->toArray();
        $activeYears = $request->get('years');

        // Проверяем, применены ли фильтры
        $hasFilters = $request->filled('category') || $request->filled('country') || $request->filled('supplier_type') ||
            $request->filled('export_market') ||
            $request->filled('years');

        // Query
        $query = Supplier::with([
            'country',
            'products'
        ]);

        // Применяем фильтры, если есть
        if ($hasFilters) {
            $query = (new SupplierFilter())->apply($query, $request);
        }

        // Получаем поставщиков
        $suppliers = $query->paginate(20)->withQueryString();

        

        return view('supplier.index', [
            'suppliers'       => $suppliers,
            'categories'      => $categories,
            'countries'       => $countries,
            'types'           => $types,
            'activeCategory'  => $activeCategory,
            'activeCountries' => $activeCountries,
            'activeTypes'     => $activeTypes,
            'hasFilters'      => $hasFilters,
            'activeExportMarkets' => $activeExportMarkets,
            'exportMarkets' => $exportMarkets,
            'activeYears' => $activeYears,
            
        ]);
    }

    public function show(Request $request, $slug)
    {
        $tabs = config('marketplace.supplier_tabs');
        $activeTab = $request->get('tab', 'profile');

        $supplier = Supplier::with([
            'country',
            'supplierReviews.order.user',
            'supplierTypes.translation',
            'reviews'
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        /*
    |--------------------------------------------------------------------------
    | Reputation & Level Logic
    |--------------------------------------------------------------------------
    */

        $score = $supplier->reputation ?? 0;

        /*
    |--------------------------------------------------------------------------
    | Reputation Level (Accessor Driven)
    |--------------------------------------------------------------------------
    */

        $level = $supplier->level;

        /*
    |--------------------------------------------------------------------------
    | Progress toward next level (optional)
    |--------------------------------------------------------------------------
    */

        $nextLevelScore = match ($level) {
            'Basic' => 51,
            'Silver' => 121,
            'Gold' => 201,
            default => max($score, 1)
        };

        $progress = min(($score / $nextLevelScore) * 100, 100);

        /*
    |--------------------------------------------------------------------------
    | Ratings
    |--------------------------------------------------------------------------
    */

        $supplierRating = round(
            $supplier->supplierReviews->avg('rating') ?? 0,
            1
        );

        $reviewsProductsCount = $supplier->reviews->count();

        /*
    |--------------------------------------------------------------------------
    | Supplier Types
    |--------------------------------------------------------------------------
    */

        $types = $supplier->supplierTypes
            ->map(
                fn($type) =>
                $type->translation?->name ?? $type->slug
            )
            ->filter()
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Years on Platform
    |--------------------------------------------------------------------------
    */

        $yearsOnPlatform = now()->diffInYears($supplier->created_at);


        /*
    |--------------------------------------------------------------------------
    | Products Query (Main List Query)
    |--------------------------------------------------------------------------
    */

        $productsQuery = Product::query()
            ->where('supplier_id', $supplier->id)
            ->with([
                'images',
                'priceTiers',
                'reviews',
                'orderItems.order',
                'category',
                'materials.translations'
            ]);

        /*
    |--------------------------------------------------------------------------
    | Apply Filter Pipeline
    |--------------------------------------------------------------------------
    */

        $productsQuery = (new \App\Filters\ProductFilter())
            ->apply($productsQuery, $request);

        /*
    |--------------------------------------------------------------------------
    | Sorting Layer
    |--------------------------------------------------------------------------
    */

        $sort = $request->get('sort', 'featured');

        if ($sort === 'price_asc') {

            $productsQuery
                ->leftJoin('price_tiers', 'price_tiers.product_id', '=', 'products.id')
                ->select('products.*')
                ->groupBy('products.id')
                ->orderByRaw('MIN(price_tiers.price) ASC');
        } elseif ($sort === 'price_desc') {

            $productsQuery
                ->leftJoin('price_tiers', 'price_tiers.product_id', '=', 'products.id')
                ->select('products.*')
                ->groupBy('products.id')
                ->orderByRaw('MIN(price_tiers.price) DESC');
        } elseif ($sort === 'newest') {

            $productsQuery->orderBy('products.created_at', 'desc');
        } else {

            $productsQuery->orderBy('products.id', 'desc');
        }

        $productsQuery->distinct('products.id');

        /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

        $products = $productsQuery
            ->paginate(25)
            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | Catalog Facet Aggregation Query (Sidebar Tree Source)
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Company catalog tree must shrink only when product filters shrink dataset.
    | Category navigation filter itself should not affect sidebar tree.
    |
    */

        $catalogFacetQuery = Product::query()
            ->where('supplier_id', $supplier->id);

        /*
     Clone request and remove category filter for navigation facet aggregation
    */

        $facetRequest = clone $request;

        $facetRequest->merge([
            'category' => null
        ]);

        $catalogFacetQuery = (new \App\Filters\ProductFilter())
            ->apply($catalogFacetQuery, $facetRequest);

        /*
    |--------------------------------------------------------------------------
    | Category Tree Facet Calculation
    |--------------------------------------------------------------------------
    */

        $categoryIds = $catalogFacetQuery
            ->pluck('category_id')
            ->filter()
            ->unique();

        $leafCategoryIds = Category::query()
            ->whereIn('id', $categoryIds)
            ->whereDoesntHave('children')
            ->pluck('id');

        $categories = Category::query()
            ->where(function ($query) use ($leafCategoryIds) {

                $query->whereIn('id', $leafCategoryIds)
                    ->orWhereHas('children', fn($q) =>
                    $q->whereIn('id', $leafCategoryIds))
                    ->orWhereHas('children.children', fn($q) =>
                    $q->whereIn('id', $leafCategoryIds));
            })
            ->orderBy('sort_order')
            ->get();

        $tree = $categories->groupBy('parent_id');

        $rootCategories = $tree[null] ?? collect();

        $supplierRating = round($supplier->supplierReviews->avg('rating'), 1);
        $count = $supplier->supplierReviews->count();
        

        return view('supplier.show', compact(
            'supplier',
            'rootCategories',
            'categoryIds',
            'tree',
            'products',
            'level',
            'progress',
            'supplierRating',
            'reviewsProductsCount',
            'types',
            'yearsOnPlatform',
            'tabs',
            'activeTab',
            'supplierRating',
            'count'
        ));
    }
}
