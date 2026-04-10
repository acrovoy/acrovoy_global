<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Domain\Filters\FilterFactory;

use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;




class CatalogController extends Controller
{
 public function index(Request $request)
{
    $locale = app()->getLocale();

    $wishlistIds = auth()->user()
    ? auth()->user()->wishlist()->pluck('products.id')->toArray()
    : [];

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
    ->with([
        'images',
        'supplier',
        'materials.translations',
        'variantGroup.items.media'
    ])
    ->withAvg('reviews', 'rating')
    ->withCount('reviews');

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

    $materials = \App\Models\Material::all();
    $countries = \App\Models\Country::all();

    $categoryId = $selectedCategory?->id;

$filterableAttributes = Attribute::with('options.translations')
    ->where('is_filterable', 1)
    ->orderBy('sort_order')
    ->get();

$textAndNumberValues = [];

// Получаем все товары текущей категории
$productsInCategory = Product::where('category_id', $categoryId)->pluck('id')->toArray();

foreach ($filterableAttributes as $attribute) {

    // --- Select / Multiselect ---
    if (in_array($attribute->type, ['select', 'multiselect'])) {

        $attribute->options = $attribute->options->filter(function ($option) use ($productsInCategory) {
            return \DB::table('product_attribute_value_options')
                ->whereIn('product_attribute_value_id', function ($query) use ($option, $productsInCategory) {
                    $query->select('id')
                        ->from('product_attribute_values')
                        ->where('attribute_id', $option->attribute_id)
                        ->whereIn('product_id', $productsInCategory);
                })
                ->where('attribute_option_id', $option->id)
                ->exists();
        });

   // --- Text / Number ---
} elseif (in_array($attribute->type, ['text', 'number'])) {

    // Получаем id значений атрибутов товаров в категории
    $values = \DB::table('product_attribute_values')
        ->join('products', 'product_attribute_values.product_id', '=', 'products.id')
        ->where('products.category_id', $categoryId)
        ->where('product_attribute_values.attribute_id', $attribute->id)
        ->pluck('product_attribute_values.id') // явно указываем таблицу
        ->toArray();

    if (!empty($values)) {
        // Получаем переводы значений
        $textAndNumberValues[$attribute->code] = \DB::table('product_attribute_value_translations')
            ->whereIn('product_attribute_value_id', $values)
            ->pluck('value')
            ->unique()
            ->toArray();
    }
}
}










    // общее количество товаров
    $totalProducts = (clone $productsQuery)->count();

    return view('catalog.index', compact(
        'catalogCategories',
        'categories',
        'products',
        'totalProducts',
        'selectedCategory',
        'types',
        'activeTypes',
        'materials',
        'countries',
        'filterableAttributes',
        'textAndNumberValues',
        'wishlistIds'
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
