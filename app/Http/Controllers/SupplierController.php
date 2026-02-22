<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Filters\SupplierFilter;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Country;
use App\Models\ExportMarket;

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
        $countries = Country::orderBy('name')->get();

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
        $suppliers = $query->get();

        $suppliers->each(function ($supplier) {
            $supplier->years_on_platform = now()->diffInYears($supplier->created_at);
        });

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
        $supplier = Supplier::with('country')
            ->where('slug', $slug)
            ->firstOrFail();

        $productsQuery = $supplier->products()->with([
            'images',
            'priceTiers',
            'reviews',
            'orderItems.order',
            'category',
            'materials.translations'
        ]);

        // TODO: применяем ProductFilter, если нужен
        // $productsQuery = (new ProductFilter())->apply($productsQuery, $request);

        $sort = $request->get('sort', 'featured');
        switch ($sort) {
            case 'price_asc':
                $productsQuery->leftJoin('price_tiers', 'price_tiers.product_id', '=', 'products.id')
                    ->select('products.*')
                    ->groupBy('products.id')
                    ->orderByRaw('MIN(price_tiers.price) ASC');
                break;
            case 'price_desc':
                $productsQuery->leftJoin('price_tiers', 'price_tiers.product_id', '=', 'products.id')
                    ->select('products.*')
                    ->groupBy('products.id')
                    ->orderByRaw('MIN(price_tiers.price) DESC');
                break;
            case 'newest':
                $productsQuery->orderBy('products.created_at', 'desc');
                break;
            default:
                $productsQuery->orderBy('products.id', 'desc');
                break;
        }

        $products = $productsQuery->get();
        $supplier->setRelation('products', $products);

        // ======================================
        // Категории поставщика для фильтров
        // ======================================
        $productsForCategories = $supplier->products()->with('category.parent', 'category.children')->get();
        $categoryIds = $productsForCategories->pluck('category_id')->filter()->unique();
        $categories = Category::with(['parent', 'children'])->whereIn('id', $categoryIds)->get();

        $allCategories = collect();
        $collectCategory = function ($cat) use (&$allCategories, &$collectCategory) {
            $allCategories->push($cat);
            if ($cat->parent) $allCategories->push($cat->parent);
            if ($cat->children->count()) {
                foreach ($cat->children as $child) {
                    $collectCategory($child);
                }
            }
        };
        foreach ($categories as $cat) {
            $collectCategory($cat);
        }
        $allCategories = $allCategories->unique('id');
        $rootCategories = $allCategories->whereNull('parent_id');

        return view('supplier.show', compact('supplier', 'rootCategories', 'categoryIds'));
    }
}