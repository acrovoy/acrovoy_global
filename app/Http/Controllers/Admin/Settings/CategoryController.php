<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Attribute;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('types', 'translations', 'parent')->orderBy('id')->get();

        $categories_map = Category::with('translations')
        ->orderBy('level')
        ->orderBy('sort_order')
        ->get()
        ->map(function($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'parent_id' => $cat->parent_id,
                'level' => $cat->level,
                'is_leaf' => $cat->is_leaf,
                'slug' => $cat->slug,
            ];
        });


        return view('dashboard.admin.settings.categories.index', compact('categories', 'categories_map'));
    }

    public function create()
    {
        $categories = Category::all();
    $attributes = Attribute::orderBy('sort_order')->get();
    return view('dashboard.admin.settings.categories.create', compact('categories', 'attributes'));
    }

    public function store(Request $request)
{
    $request->validate([
        'is_selectable' => 'nullable|boolean',
        'is_leaf' => 'nullable|boolean',
        'sort_order' => 'nullable|integer|min:0',
        'slug' => 'required|string|max:255|unique:categories,slug',
        'parent_id' => 'nullable|exists:categories,id',
        'name.*' => 'required|string|max:255',

        // NEW
        'types' => 'nullable|array',
        'types.*' => 'in:product,rfq,project',
    ]);

    DB::transaction(function () use ($request) {

        $level = 0;
        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            $level = $parent->level + 1;
        }

        $category = Category::create([
            'slug' => $request->slug,
            'parent_id' => $request->parent_id,
            'level' => $level,
            'commission_percent' => $request->commission_percent,

            // ❌ OLD: 'type' => $request->type,

            'is_selectable' => $request->has('is_selectable') ? 1 : 0,
            'is_leaf' => $request->has('is_leaf') ? 1 : 0,
            'sort_order' => $request->input('sort_order', 0),
        ]);

        $category->attributes()->sync($request->input('attributes', []));

        // NEW: types save
        if ($request->filled('types')) {
            foreach ($request->types as $type) {
                $category->types()->create([
                    'type' => $type
                ]);
            }
        }

        foreach ($request->name as $locale => $name) {
            $category->translations()->create([
                'locale' => $locale,
                'name' => $name,
            ]);
        }
    });

    return redirect()
        ->route('admin.settings.categories.index')
        ->with('success', 'Category created');
}

    public function edit(Category $category)
{
    $categories = Category::where('id', '!=', $category->id)->get();

    $attributes = Attribute::orderBy('sort_order')->get();

    // NEW: подгружаем типы категории
    $category->load('types');

    return view(
        'dashboard.admin.settings.categories.edit',
        compact('category', 'categories', 'attributes')
    );
}

    public function update(Request $request, Category $category)
{
    $request->validate([
        'is_selectable' => 'nullable|boolean',
        'is_leaf' => 'nullable|boolean',
        'sort_order' => 'nullable|integer|min:0',
        'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
        'parent_id' => 'nullable|exists:categories,id',
        'name.*' => 'required|string|max:255',

        // NEW
        'types' => 'nullable|array',
        'types.*' => 'in:product,rfq,project',
    ]);

    DB::transaction(function () use ($request, $category) {

        $level = 0;
        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            $level = $parent->level + 1;
        }

        $category->update([
            'slug' => $request->slug,
            'parent_id' => $request->parent_id,
            'level' => $level,
            'commission_percent' => $request->commission_percent,

            // ❌ OLD: 'type' => $request->type,

            'is_selectable' => $request->has('is_selectable') ? 1 : 0,
            'is_leaf' => $request->has('is_leaf') ? 1 : 0,
            'sort_order' => $request->input('sort_order', 0),
        ]);

        $category->attributes()->sync($request->input('attributes', []));

        // NEW: replace all types
        $category->types()->delete();

        if ($request->filled('types')) {
            foreach ($request->types as $type) {
                $category->types()->create([
                    'type' => $type
                ]);
            }
        }

        foreach ($request->name as $locale => $name) {
            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                ['name' => $name]
            );
        }
    });

    return redirect()
        ->route('admin.settings.categories.index')
        ->with('success', 'Category updated');
}

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with products');
        }

        $category->delete();
        return redirect()->route('admin.settings.categories.index')->with('success', 'Category deleted');
    }


    public function children($parentId)
{
    return Category::where('parent_id', $parentId)
        ->orderBy('sort_order')
        ->get([
            'id',
            'name',
            'is_selectable'
        ]);
}


public function root()
{
    return Category::whereNull('parent_id')
        ->orderBy('sort_order')
        ->get([
            'id',
            'name',
            'is_selectable'
        ]);
}


}
