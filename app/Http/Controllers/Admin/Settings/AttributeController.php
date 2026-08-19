<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Attribute;
use App\Models\AttributeTranslation;
use App\Models\Unit;
use App\Models\AttributeGroup;


class AttributeController extends Controller
{
    public function index(Request $request)
{
    $query = Attribute::query()
        ->with('translations');

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {

        $search = trim($request->input('search'));

        $query->where(function ($q) use ($search) {

            $q->where('code', 'like', "%{$search}%")
                ->orWhereHas('translations', function ($translationQuery) use ($search) {

                    $translationQuery->where('name', 'like', "%{$search}%");

                });

        });
    }


    /*
    |--------------------------------------------------------------------------
    | ENTITY TYPE
    |--------------------------------------------------------------------------
    */

    if ($request->filled('entity_type')) {

        $query->where(
            'entity_type',
            $request->input('entity_type')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE TYPE
    |--------------------------------------------------------------------------
    */

    if ($request->filled('type')) {

        $query->where(
            'type',
            $request->input('type')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NEW
    |--------------------------------------------------------------------------
    */

    if ($request->boolean('new')) {
        $query->where('created_at', '>=', now()->subDays(7));
    }


    /*
    |--------------------------------------------------------------------------
    | SORTING
    |--------------------------------------------------------------------------
    */

    $allowedSorts = [
        'name',
        'code',
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
    |
    | Name находится в translations, поэтому сортируем через subquery.
    |
    */

    if ($sort === 'name') {

        $query->orderBy(
            AttributeTranslation::select('name')
                ->whereColumn(
                    'attribute_translations.attribute_id',
                    'attributes.id'
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
    | GET ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    $attributes = $query->get();


    /*
    |--------------------------------------------------------------------------
    | SPLIT SYSTEM / CUSTOM
    |--------------------------------------------------------------------------
    */

    $systemAttributes = $attributes
        ->where('is_custom', false)
        ->values();

    $customAttributes = $attributes
        ->where('is_custom', true)
        ->values();

        

          /*
    |--------------------------------------------------------------------------
    | NEW CUSTOM ATTRIBUTES
    |--------------------------------------------------------------------------
    |
    | Custom attributes created during the last 7 days.
    |
    */

    $newCustomSince = now()->subDays(7);

    $newCustomAttributes = $customAttributes
        ->filter(function ($attribute) use ($newCustomSince) {

            return $attribute->created_at
                && $attribute->created_at->gte($newCustomSince);

        })
        ->values();


    /*
    |--------------------------------------------------------------------------
    | NEW CUSTOM ATTRIBUTES COUNT
    |--------------------------------------------------------------------------
    */

    $newCustomAttributesCount = $newCustomAttributes->count();


    return view(
        'dashboard.admin.settings.attributes.index',
        compact(
            'systemAttributes',
            'customAttributes',
            'newCustomAttributes',
            'newCustomAttributesCount'
        )
    );
}




    public function create()
    {

     $units = Unit::query()
        ->where('is_active', true)
        ->with('translations')
        ->orderBy('unit_group')
        ->orderBy('sort_order')
        ->get()
        ->groupBy('unit_group');

    $attributeGroups = AttributeGroup::query()
        ->where('is_active', true)
        ->whereNull('owner_type')
        ->whereNull('owner_id')
        ->orderBy('name')
        ->get();

        return view(
            'dashboard.admin.settings.attributes.create',
        compact('units', 'attributeGroups')
        );
    }


    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:attributes,code',
            'type' => 'required',
            'translations' => 'required|array',
            'unit_id' => [
                'nullable',
                'integer',
                'exists:units,id',
                'required_if:type,measurement',
            ],
            'group_id' => [
                'nullable',
                'integer',
                'exists:attribute_groups,id',
            ],
        ]);


        $attribute = Attribute::create([
            'code' => $request->code,
            'type' => $request->type,
            'entity_type' => $request->entity_type,
            'context' => $request->context,
            'group_id'    => $request->group_id,
            'unit_id' => $request->input('unit_id'),
            'is_required' => $request->boolean('is_required'),
            'is_filterable' => $request->boolean('is_filterable'),
            'is_offerable' => $request->boolean('is_offerable'),
            'is_custom' => $request->boolean('is_custom'),
            'owner_type' => NULL,
            'owner_id' => NULL,
            'sort_order' => $request->sort_order ?? 0,
            'created_by' => auth()->id(),
        ]);


        foreach ($request->translations as $locale => $name) {

            if (!$name) continue;

            AttributeTranslation::create([
                'attribute_id' => $attribute->id,
                'locale' => $locale,
                'name' => $name
            ]);
        }


        return redirect()
            ->route('admin.settings.attributes.index')
            ->with('success', 'Attribute created');
    }


    public function edit(Attribute $attribute)
{
    $units = Unit::query()
        ->where('is_active', true)
        ->with('translations')
        ->orderBy('unit_group')
        ->orderBy('sort_order')
        ->get()
        ->groupBy('unit_group');

    $attributeGroups = AttributeGroup::query()
        ->where('is_active', true)
        ->whereNull('owner_type')
        ->whereNull('owner_id')
        ->orderBy('name')
        ->get();

    return view(
        'dashboard.admin.settings.attributes.edit',
        compact('attribute', 'units', 'attributeGroups')
    );
}


    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'code' => 'required|unique:attributes,code,' . $attribute->id,
            'type' => 'required',
            'translations' => 'required|array',
            'unit_id' => [
                'nullable',
                'integer',
                'exists:units,id',
                'required_if:type,measurement',
            ],
            'group_id' => [
                'nullable',
                'integer',
                'exists:attribute_groups,id',
            ],
        ]);


        $attribute->update([
            'code' => $request->code,
            'type' => $request->type,
            'entity_type' => $request->entity_type,
            'context' => $request->context,
            'group_id' => $request->input('group_id'),
            'unit' => $request->unit,
            'unit_id' => $request->input('unit_id'),
            'is_required' => $request->boolean('is_required'),
            'is_filterable' => $request->boolean('is_filterable'),
            'is_offerable' => $request->boolean('is_offerable'),
            'is_custom' => $request->boolean('is_custom'),
            'owner_type' => $request->owner_type,
            'owner_id' => $request->owner_id,
            'sort_order' => $request->sort_order ?? 0,
            
        ]);


        AttributeTranslation::where(
            'attribute_id',
            $attribute->id
        )->delete();


        foreach ($request->translations as $locale => $name) {

            if (!$name) continue;

            AttributeTranslation::create([
                'attribute_id' => $attribute->id,
                'locale' => $locale,
                'name' => $name
            ]);
        }


        return redirect()
            ->route('admin.settings.attributes.index')
            ->with('success', 'Attribute updated');
    }


    public function destroy(Attribute $attribute)
    {
        $attribute->translations()->delete();
        $attribute->delete();

        return back()->with(
            'success',
            'Attribute deleted'
        );
    }
}
