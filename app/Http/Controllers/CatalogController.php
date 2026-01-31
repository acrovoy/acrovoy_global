<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Filters\ProductFilter;

use App\Models\Category;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index(Request $request, ProductFilter $filter)
    {
        $locale = app()->getLocale();

        // Mega Menu
        $catalogCategories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('name', 'desc')
            ->get();

        $selectedCategory = $request->category
            ? Category::with('parent', 'children')->where('slug', $request->category)->first()
            : null;

        $categories = $selectedCategory
            ? $selectedCategory->parent
                ? $selectedCategory->parent->children->where('id', '!=', $selectedCategory->id)
                : $selectedCategory->children->where('id', '!=', $selectedCategory->id)
            : Category::with('children')->whereNull('parent_id')->get();

        // Продукты через ProductFilter + withBaseRelations + агрегаты
        $products = $filter->apply(
            Product::query()->withBaseRelations(),
            $request
        )->paginate(24);

        $totalProducts = Product::count();

        return view('catalog.index', compact(
            'catalogCategories',
            'categories',
            'products',
            'totalProducts',
            'selectedCategory'
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
