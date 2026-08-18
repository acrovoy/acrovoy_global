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
    public function index(Request $request)
{
    $query = Category::with('types', 'translations', 'parent');

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {
        $search = trim($request->input('search'));

        $query->where(function ($q) use ($search) {
            $q->where('slug', 'like', "%{$search}%")
                ->orWhereHas('translations', function ($translationQuery) use ($search) {
                    $translationQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PARENT CATEGORY
    |--------------------------------------------------------------------------
    */

    if ($request->filled('parent_id')) {
        $query->where('parent_id', $request->input('parent_id'));
    }

    /*
    |--------------------------------------------------------------------------
    | LEVEL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('level')) {
        $query->where('level', $request->input('level'));
    }

    /*
    |--------------------------------------------------------------------------
    | SELECTABLE
    |--------------------------------------------------------------------------
    */

    if ($request->filled('is_selectable')) {
        $query->where('is_selectable', $request->boolean('is_selectable'));
    }

    /*
    |--------------------------------------------------------------------------
    | LEAF
    |--------------------------------------------------------------------------
    */

    if ($request->filled('is_leaf')) {
        $query->where('is_leaf', $request->boolean('is_leaf'));
    }

    /*
    |--------------------------------------------------------------------------
    | VISIBLE
    |--------------------------------------------------------------------------
    */

    if ($request->filled('is_visible')) {
        $query->where('is_visible', $request->boolean('is_visible'));
    }

    /*
    |--------------------------------------------------------------------------
    | SORTING
    |--------------------------------------------------------------------------
    */

    $allowedSorts = [
        'name',
        'slug',
        'level',
        'sort_order',
        'created_at',
    ];

    $sort = $request->input('sort', 'sort_order');

    if (!in_array($sort, $allowedSorts, true)) {
        $sort = 'sort_order';
    }

    $direction = $request->input('direction', 'asc');

    if (!in_array($direction, ['asc', 'desc'], true)) {
        $direction = 'asc';
    }

    /*
    |--------------------------------------------------------------------------
    | SORT BY TRANSLATED NAME
    |--------------------------------------------------------------------------
    */

    if ($sort === 'name') {
        $query->orderBy(
            CategoryTranslation::select('name')
                ->whereColumn(
                    'category_translations.category_id',
                    'categories.id'
                )
                ->where('locale', app()->getLocale())
                ->limit(1),
            $direction
        );
    } else {
        $query->orderBy($sort, $direction);
    }

    /*
    |--------------------------------------------------------------------------
    | SECONDARY SORT
    |--------------------------------------------------------------------------
    */

    $query->orderBy('id', 'asc');

    /*
    |--------------------------------------------------------------------------
    | GET CATEGORIES
    |--------------------------------------------------------------------------
    */

    $categories = $query->get();

    /*
    |--------------------------------------------------------------------------
    | CATEGORIES MAP
    |--------------------------------------------------------------------------
    |
    | Used for category tree / parent category selection.
    | It must contain all categories regardless of active filters.
    |
    */

    $categories_map = Category::with('translations')
        ->orderBy('level')
        ->orderBy('sort_order')
        ->get()
        ->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'parent_id' => $cat->parent_id,
                'level' => $cat->level,
                'is_leaf' => $cat->is_leaf,
                'slug' => $cat->slug,
            ];
        });

    return view(
        'dashboard.admin.settings.categories.index',
        compact(
            'categories',
            'categories_map'
        )
    );
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

        'name' => 'required|array',
        'name.*' => 'required|string|max:255',

        'is_visible' => 'nullable|boolean',

        'commission_percent' => 'nullable|numeric|min:0|max:100',

        /*
        |--------------------------------------------------------------------------
        | CATEGORY TYPES
        |--------------------------------------------------------------------------
        */

        'types' => 'nullable|array',
        'types.*' => 'in:product,rfq,project',

        /*
        |--------------------------------------------------------------------------
        | CATEGORY ATTRIBUTES
        |--------------------------------------------------------------------------
        |
        | Expected structure:
        |
        | attributes[93][enabled]
        | attributes[93][is_required]
        | attributes[93][sort_order]
        |
        */

        'attributes' => 'nullable|array',

        'attributes.*.enabled' => 'nullable|boolean',

        'attributes.*.is_required' => 'nullable|boolean',

        'attributes.*.sort_order' => 'nullable|integer|min:0',
    ]);


    DB::transaction(function () use ($request) {

        /*
        |--------------------------------------------------------------------------
        | CATEGORY LEVEL
        |--------------------------------------------------------------------------
        */

        $level = 0;

        if ($request->parent_id) {

            $parent = Category::findOrFail($request->parent_id);

            $level = $parent->level + 1;
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE CATEGORY
        |--------------------------------------------------------------------------
        */

        $category = Category::create([

            'slug' => $request->slug,

            'parent_id' => $request->parent_id,

            'level' => $level,

            'commission_percent' => $request->input(
                'commission_percent',
                0
            ),

            'is_selectable' => $request->boolean(
                'is_selectable'
            ),

            'is_leaf' => $request->boolean(
                'is_leaf'
            ),

            'is_visible' => $request->boolean(
                'is_visible'
            ),

            'sort_order' => $request->input(
                'sort_order',
                0
            ),
        ]);


        /*
        |--------------------------------------------------------------------------
        | CATEGORY ATTRIBUTES
        |--------------------------------------------------------------------------
        */

        $attributeSync = [];


        foreach ($request->input('attributes', []) as $attributeId => $data) {

            /*
             * Attribute checkbox must be enabled.
             */
            if (!isset($data['enabled']) || !$data['enabled']) {
                continue;
            }


            $attributeSync[$attributeId] = [

                /*
                 * Is this attribute required for this category?
                 */
                'is_required' => !empty($data['is_required'])
                    ? 1
                    : 0,


                /*
                 * Attribute position inside category.
                 */
                'sort_order' => isset($data['sort_order'])
                    ? (int) $data['sort_order']
                    : 0,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | SAVE CATEGORY ATTRIBUTES
        |--------------------------------------------------------------------------
        */

        if (!empty($attributeSync)) {

            $category->attributes()->sync($attributeSync);

        } else {

            $category->attributes()->sync([]);

        }


        /*
        |--------------------------------------------------------------------------
        | CATEGORY CONTEXTS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('types')) {

            foreach ($request->types as $type) {

                $category->types()->create([
                    'type' => $type,
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSLATIONS
        |--------------------------------------------------------------------------
        */

        foreach ($request->input('name', []) as $locale => $name) {

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
    $categories = Category::where('id', '!=', $category->id)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    |
    | Загружаем все доступные атрибуты.
    | pivot содержит настройки конкретного атрибута
    | для конкретной категории:
    |
    | - is_required
    | - sort_order
    |
    */

    $attributes = Attribute::where('is_custom', false)
    ->orderBy('sort_order')
    ->get();


    /*
    |--------------------------------------------------------------------------
    | CATEGORY TYPES
    |--------------------------------------------------------------------------
    */

    $category->load('types');


    /*
    |--------------------------------------------------------------------------
    | CATEGORY ATTRIBUTES
    |--------------------------------------------------------------------------
    |
    | Загружаем атрибуты категории вместе с pivot.
    |
    */

    $category->load([
        'attributes' => function ($query) {
            $query->withPivot([
                'is_required',
                'sort_order',
            ]);
        },
    ]);


    return view(
        'dashboard.admin.settings.categories.edit',
        compact(
            'category',
            'categories',
            'attributes'
        )
    );
}

    public function update(Request $request, Category $category)
{
    $request->validate([
        'is_selectable' => 'nullable|boolean',
        'is_leaf' => 'nullable|boolean',
        'sort_order' => 'nullable|integer|min:0',
        'is_visible' => 'nullable|boolean',

        'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,

        'parent_id' => 'nullable|exists:categories,id',

        'name' => 'required|array',
        'name.*' => 'required|string|max:255',

        'commission_percent' => 'nullable|numeric|min:0|max:100',

        /*
        |--------------------------------------------------------------------------
        | CATEGORY TYPES
        |--------------------------------------------------------------------------
        */

        'types' => 'nullable|array',
        'types.*' => 'in:product,rfq,project',

        /*
        |--------------------------------------------------------------------------
        | CATEGORY ATTRIBUTES
        |--------------------------------------------------------------------------
        |
        | Expected:
        |
        | attributes[93][enabled]
        | attributes[93][is_required]
        | attributes[93][sort_order]
        |
        */

        'attributes' => 'nullable|array',

        'attributes.*.enabled' => 'nullable|boolean',

        'attributes.*.is_required' => 'nullable|boolean',

        'attributes.*.sort_order' => 'nullable|integer|min:0',
    ]);


    DB::transaction(function () use ($request, $category) {

        /*
        |--------------------------------------------------------------------------
        | CATEGORY LEVEL
        |--------------------------------------------------------------------------
        */

        $level = 0;

        if ($request->parent_id) {

            $parent = Category::findOrFail($request->parent_id);

            $level = $parent->level + 1;
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE CATEGORY
        |--------------------------------------------------------------------------
        */

        $category->update([

            'slug' => $request->slug,

            'parent_id' => $request->parent_id,

            'level' => $level,

            'commission_percent' => $request->input(
                'commission_percent',
                0
            ),

            'is_selectable' => $request->boolean(
                'is_selectable'
            ),

            'is_leaf' => $request->boolean(
                'is_leaf'
            ),

            'is_visible' => $request->boolean(
                'is_visible'
            ),

            'sort_order' => $request->input(
                'sort_order',
                0
            ),
        ]);


        /*
        |--------------------------------------------------------------------------
        | CATEGORY ATTRIBUTES
        |--------------------------------------------------------------------------
        |
        | Build pivot data:
        |
        | attribute_id => [
        |     is_required => 0/1,
        |     sort_order   => integer,
        | ]
        |
        */

        $attributeSync = [];


        foreach ($request->input('attributes', []) as $attributeId => $data) {

            /*
             * Only enabled attributes are attached
             * to the category.
             */

            if (
                !isset($data['enabled']) ||
                !$data['enabled']
            ) {
                continue;
            }


            $attributeSync[$attributeId] = [

                /*
                 * Required for this category
                 */

                'is_required' => !empty($data['is_required'])
                    ? 1
                    : 0,


                /*
                 * Display order inside category
                 */

                'sort_order' => isset($data['sort_order'])
                    ? (int) $data['sort_order']
                    : 0,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | SYNC CATEGORY ATTRIBUTES
        |--------------------------------------------------------------------------
        |
        | sync() will:
        |
        | - add new attributes
        | - update is_required / sort_order
        | - remove unchecked attributes
        |
        */

        $category->attributes()->sync($attributeSync);


        /*
        |--------------------------------------------------------------------------
        | CATEGORY TYPES
        |--------------------------------------------------------------------------
        |
        | Replace existing contexts.
        |
        */

        $category->types()->delete();


        if ($request->filled('types')) {

            foreach ($request->types as $type) {

                $category->types()->create([
                    'type' => $type,
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CATEGORY TRANSLATIONS
        |--------------------------------------------------------------------------
        */

        foreach ($request->input('name', []) as $locale => $name) {

            $category->translations()->updateOrCreate(

                [
                    'locale' => $locale,
                ],

                [
                    'name' => $name,
                ]
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
