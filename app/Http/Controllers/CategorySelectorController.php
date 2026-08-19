<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;

class CategorySelectorController extends Controller
{

    public function root()
    {
        return Category::root()
            ->ordered()
            ->get([
                'id',
                'slug',
                'children_count',
                'is_leaf',
                'is_selectable'
            ])
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->translation()?->name,
                'has_children' => $c->children_count > 0,
                'is_selectable' => $c->is_selectable
            ]);
    }


    public function children($parentId)
    {
        return Category::where('parent_id', $parentId)
            ->ordered()
            ->get([
                'id',
                'slug',
                'children_count',
                'is_leaf',
                'is_selectable'
            ])
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->translation()?->name ?? $c->slug,
                'has_children' => $c->children_count > 0,
                'is_selectable' => $c->is_selectable
            ]);
    }

    public function getPath($id)
    {
        $category = Category::findOrFail($id);

        $path = [];

        // Собираем путь вверх через родителя
        $current = $category;
        while ($current) {
            $children = $current->children()->ordered()->get()->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->translation()?->name ?? $c->slug,
                'children_count' => $c->children_count
            ]);
            $path[] = [
                'id' => $current->id,
                'name' => $current->translation()?->name ?? $current->slug,
                'children' => $children
            ];
            $current = $current->parent;
        }

        // Разворачиваем, чтобы идти от корня к выбранной категории
        $path = array_reverse($path);

        return response()->json($path);
    }

    public function attributes($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $productId = request()->query('product_id');
        $product = null;

        if ($productId) {
            $product = Product::with([
                'attributeValues.translations',
                'attributeValues.options.option.translations',
                'attributeValues.unit',
            ])->find($productId);
        }

        $attributes = $category->attributes()
            ->with([
                'translations',

                'options' => fn($query) =>
                $query->orderBy('sort_order'),

                'options.translations',

                'unit.translations',

                'attributeGroup',
                'attributeGroup.translations',
            ])
            ->orderBy('category_attributes.sort_order')
            ->get([
                'attributes.id',
                'attributes.code',
                'attributes.type',
                'attributes.unit_id',
                'attributes.group_id',
            ]);

        /*
    |--------------------------------------------------------------------------
    | LOAD MEASUREMENT UNITS
    |--------------------------------------------------------------------------
    |
    | For measurement attributes we load all active units
    | from the same unit group as the attribute's default unit.
    |
    */


        \Log::info('CATEGORY ATTRIBUTES DEBUG', [
            'category_id' => $categoryId,
            'attributes' => $attributes->map(function ($attr) {
                return [
                    'id' => $attr->id,
                    'code' => $attr->code,
                    'type' => $attr->type,
                    'group_id' => $attr->group_id,
                    'attribute_group' => $attr->attributeGroup ? [
                        'id' => $attr->attributeGroup->id,
                        'name' => $attr->attributeGroup->name,
                    ] : null,
                    'pivot' => [
                        'is_required' => $attr->pivot->is_required ?? null,
                        'sort_order' => $attr->pivot->sort_order ?? null,
                    ],
                ];
            })->toArray(),
        ]);


        $measurementGroups = $attributes
            ->filter(
                fn($attr) =>
                $attr->type === 'measurement' &&
                    $attr->unit
            )
            ->pluck('unit.unit_group')
            ->filter()
            ->unique();

        $measurementUnits = Unit::query()
            ->active()
            ->whereIn('unit_group', $measurementGroups)
            ->with('translations')
            ->ordered()
            ->get()
            ->groupBy('unit_group');






        $attributes = $attributes->map(function ($attr) use (
            $product,
            $measurementUnits
        ) {

            $value = null;

            $selectedUnitId = $attr->unit_id;


            /*
        |--------------------------------------------------------------------------
        | PRODUCT VALUE
        |--------------------------------------------------------------------------
        */

            if ($product) {

                $pav = $product->attributeValues
                    ->firstWhere('attribute_id', $attr->id);

                if ($pav) {

                    if ($attr->type === 'multiselect') {

                        $value = $pav->options
                            ->map(
                                fn($option) =>
                                $option->attribute_option_id
                            )
                            ->toArray();
                    } elseif ($attr->type === 'select') {

                        $value = $pav->options
                            ->first()?->attribute_option_id;
                    } elseif ($attr->type === 'boolean') {

                        $value = (int) (
                            $pav->translations
                            ->firstWhere('locale', app()->getLocale())
                            ?->value
                            ??
                            $pav->translations
                            ->first()?->value
                        );
                    } else {

                        $value =
                            $pav->translations
                            ->firstWhere(
                                'locale',
                                app()->getLocale()
                            )
                            ?->value
                            ??
                            $pav->translations
                            ->first()?->value;
                    }


                    /*
                |--------------------------------------------------------------------------
                | MEASUREMENT UNIT STORED WITH PRODUCT VALUE
                |--------------------------------------------------------------------------
                |
                | If later your AttributeValue has unit_id,
                | this will automatically use it.
                |
                */

                    if ($attr->type === 'measurement') {

                        $selectedUnitId =
                            $pav->unit_id
                            ?? $attr->unit_id;
                    }
                }
            }


            /*
        |--------------------------------------------------------------------------
        | MEASUREMENT UNITS
        |--------------------------------------------------------------------------
        */

            $units = null;

            if (
                $attr->type === 'measurement' &&
                $attr->unit
            ) {

                $units = $measurementUnits
                    ->get($attr->unit->unit_group, collect())
                    ->map(fn($unit) => [
                        'id' => $unit->id,
                        'code' => $unit->code,
                        'symbol' => $unit->symbol,
                        'name' => $unit->name,
                    ])
                    ->values()
                    ->toArray();
            }


            /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

            return [

                'id' => $attr->id,

                'code' => $attr->code,

                'name' =>
                $attr->translation()?->name
                    ?? '—',

                'type' => $attr->type,


                'group' => $attr->attributeGroup
                ? [
                    'id' => $attr->attributeGroup->id,
                    'name' => $attr->attributeGroup->translation()?->name
                        ?? $attr->attributeGroup->name
                        ?? '—',
                ]
                : null,

                /*
            |--------------------------------------------------------------------------
            | DEFAULT UNIT
            |--------------------------------------------------------------------------
            */

                'unit' => $attr->unit
                    ? [
                        'id' => $attr->unit->id,
                        'code' => $attr->unit->code,
                        'symbol' => $attr->unit->symbol,
                        'name' => $attr->unit->name,
                    ]
                    : null,


                /*
            |--------------------------------------------------------------------------
            | AVAILABLE MEASUREMENT UNITS
            |--------------------------------------------------------------------------
            */

                'units' => $units,


                /*
            |--------------------------------------------------------------------------
            | SELECT OPTIONS
            |--------------------------------------------------------------------------
            */

                'options' => $attr->options
                    ? collect($attr->options)
                    ->map(fn($o) => [
                        'value' => $o->id,
                        'label' => $o->translatedValue(),
                    ])
                    ->toArray()
                    : null,


                /*
            |--------------------------------------------------------------------------
            | FLAGS
            |--------------------------------------------------------------------------
            */

                'is_required' =>
                $attr->pivot->is_required ?? false,


                'multi_locale_input' =>
                $attr->type === 'text',


                /*
            |--------------------------------------------------------------------------
            | VALUE
            |--------------------------------------------------------------------------
            */

                'value' => $value,


                /*
            |--------------------------------------------------------------------------
            | SELECTED UNIT
            |--------------------------------------------------------------------------
            */

                'selected_unit_id' =>
                $selectedUnitId,
            ];
        });

        return response()->json($attributes);
    }
}
