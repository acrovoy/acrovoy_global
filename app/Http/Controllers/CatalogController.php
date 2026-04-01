<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Domain\Filters\FilterFactory;

use App\Models\Category;
use App\Models\Product;

class CatalogController extends Controller
{
 public function index(Request $request)
{
    $locale = app()->getLocale();

    // Mega Menu (верхний каталог)
    $catalogCategories = Category::with('children')
        ->whereNull('parent_id')
        ->orderBy('name', 'desc')
        ->get();

    // slug категории приходит через route или GET-параметр
    $categorySlug = $request->route('category') ?? $request->query('category');

    // текущая выбранная категория
    $selectedCategory = $categorySlug
        ? Category::with('parent', 'children.children')
            ->where('slug', $categorySlug)
            ->first()
        : null;

    // определяем leaf категория или нет
    $isLeafCategory = $selectedCategory
        ? $selectedCategory->children->isEmpty()
        : false;

    // Sidebar категории
    if (!$selectedCategory) {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->get();
    } elseif (!$isLeafCategory) {
        // если категория parent → показываем её children
        $categories = $selectedCategory->children;
    } elseif ($selectedCategory->parent) {
        // если leaf → показываем соседние категории
        $categories = $selectedCategory->parent
            ->children
            ->where('id', '!=', $selectedCategory->id);
    } else {
        $categories = collect();
    }

    // создаём query товаров
    $productsQuery = Product::query()
        ->withBaseRelations()
        ->with(['images', 'supplier']);

    // собираем параметры фильтра pipeline
    $params = $request->all();
    if ($categorySlug) {
        $params['category'] = $categorySlug;
    }

    // запускаем pipeline фильтров
    $pipeline = (new FilterFactory(config('product_filters')))
        ->make($params);
    $productsQuery = $pipeline->apply($productsQuery, $params);

    // supplier types (Top Filter Bar)
    $activeTypes = $request->supplier_type ?? [];
    $types = config('product_supplier_types');

    // Инициализируем переменные для лендинга
    $subcategories = collect();
    $premiumProducts = collect();
    $topProducts = collect();

    if ($selectedCategory && !$isLeafCategory) {
        // Подкатегории
        $subcategories = $selectedCategory->children;

        return view('catalog.category_landing', compact(
            'selectedCategory',
            'catalogCategories',
            'categories',
            'isLeafCategory',
            'subcategories'
        ));
    }

    // пагинация товаров (leaf категории)
    $products = $productsQuery->paginate(24);

    // общее количество товаров
    $totalProducts = Product::count();

    return view('catalog.index', compact(
        'catalogCategories',
        'categories',
        'products',
        'totalProducts',
        'selectedCategory',
        'types',
        'activeTypes'
    ));
}




    public function setCountry($country)
    {
        $allowedCountries = ['us', 'uk', 'de', 'fr', 'ua', 'sa'];

        if (!in_array($country, $allowedCountries)) {
            abort(404);
        }

        session(['search_country' => $country]);

        return redirect()->back();
    }
}
