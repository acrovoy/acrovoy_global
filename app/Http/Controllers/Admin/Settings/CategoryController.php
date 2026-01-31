<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Category;
use App\Models\CategoryTranslation;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('translations', 'parent')->orderBy('id')->get();
        return view('dashboard.admin.settings.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::all(); // для выбора parent
        return view('dashboard.admin.settings.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'name.*' => 'required|string|max:255',
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
                'type' => $request->type,
            ]);

            foreach ($request->name as $locale => $name) {
                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }
        });

        return redirect()->route('admin.settings.categories.index')->with('success', 'Category created');
    }

    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->get(); // нельзя выбрать себя как parent
        return view('dashboard.admin.settings.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'name.*' => 'required|string|max:255',
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
                'type' => $request->type,
            ]);

            foreach ($request->name as $locale => $name) {
                $category->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['name' => $name]
                );
            }
        });

        return redirect()->route('admin.settings.categories.index')->with('success', 'Category updated');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with products');
        }

        $category->delete();
        return redirect()->route('admin.settings.categories.index')->with('success', 'Category deleted');
    }
}
