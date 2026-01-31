<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Filters\SupplierFilter;
use App\Filters\ProductFilter;

use App\Models\Supplier;
use App\Models\Product;
use App\Models\Category;


class SupplierController extends Controller
{

    public function index(Request $request)
{
    // Загружаем категории для sidebar
    $categories = Category::all();

    // Базовый запрос с подсчетом проданных товаров
    $query = Supplier::withCount('products as sold_count');

    // Применяем фильтры
    $suppliers = (new SupplierFilter())->apply($query, $request)->get();

    // Список типов поставщиков (например премиум, стандарт)
    $types = [
        'premium' => 'Premium',
        'standard' => 'Standard',
        'new' => 'New',
    ];

    return view('supplier.index', compact('suppliers', 'categories', 'types'));
}

    public function show(Request $request, $slug)
{
    // Загружаем поставщика с основной инфой
    $supplier = Supplier::with('country')
        ->where('slug', $slug)
        ->firstOrFail();

    // Создаём Builder для продуктов — важно: именно Builder, а не HasMany
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

    // Применяем фильтры через существующий ProductFilter
    $productsQuery = (new ProductFilter())->apply($productsQuery, $request);

    // Сортировка
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

    // Получаем продукты
    $products = $productsQuery->get();

    // Считаем sold_count для каждого продукта (хоть в withSum тоже можно, но так безопасно)
    $products = $products->map(function ($product) {
        $soldCount = $product->orderItems
            ->filter(fn($item) => $item->order && $item->order->status === 'completed')
            ->sum('quantity');
        $product->sold_count = $soldCount;
        return $product;
    });

    // Сохраняем relation для Blade
    $supplier->setRelation('products', $products);

     /**
     * =====================================================
     * 🟢 КАТЕГОРИИ ПРОДАВЦА (ЛОГИКА ИЗ BLADE)
     * =====================================================
     */

     // Продукты продавца для категорий — уже загружены
$productsForCategories = Product::where('supplier_id', $supplier->id)
    ->with('category.parent', 'category.children')
    ->get();

// ID категорий продуктов
$categoryIds = $productsForCategories->pluck('category_id')->filter()->unique();

// Загружаем категории
$categories = Category::with(['parent', 'children'])->whereIn('id', $categoryIds)->get();

// Добавляем родителей и рекурсивно собираем всех потомков
$allCategories = collect();

$collectCategory = function ($cat) use (&$allCategories, &$collectCategory) {
    $allCategories->push($cat);

    // Добавляем родителя, если есть
    if ($cat->parent) {
        $allCategories->push($cat->parent);
    }

    // Рекурсивно добавляем детей
    if ($cat->children->count()) {
        foreach ($cat->children as $child) {
            $collectCategory($child);
        }
    }
};

// Применяем сбор к всем категориям товаров
foreach ($categories as $cat) {
    $collectCategory($cat);
}

// Убираем дубликаты
$allCategories = $allCategories->unique('id');

// Корневые категории
$rootCategories = $allCategories->whereNull('parent_id');

    return view('supplier.show', compact('supplier', 'rootCategories', 'categoryIds'));
}




}
