<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

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
        ])->find($productId);
    }

    $attributes = $category->attributes()
        ->with(['translations', 'options.translations'])
        ->orderBy('category_attributes.sort_order')
        ->get(['attributes.id', 'attributes.type', 'attributes.unit']);

    $attributes = $attributes->map(function ($attr) use ($product) {
        $value = null;

        if ($product) {
            $pav = $product->attributeValues->firstWhere('attribute_id', $attr->id);

            if ($pav) {
                if ($attr->type === 'multiselect') {
                    $value = $pav->options->map(fn($option) => $option->attribute_option_id)->toArray();
                } elseif ($attr->type === 'select') {
                    $value = $pav->options->first()?->attribute_option_id;
                } elseif ($attr->type === 'boolean') {
                    $value = (int) ($pav->translations->firstWhere('locale', app()->getLocale())?->value ?? $pav->translations->first()?->value);
                } else {
                    $value = $pav->translations->firstWhere('locale', app()->getLocale())?->value
                        ?? $pav->translations->first()?->value;
                }
            }
        }

        return [
            'id' => $attr->id,
            'name' => $attr->translation()?->name ?? '—',
            'type' => $attr->type,
            'unit' => $attr->unit,
            'options' => $attr->options
                ? collect($attr->options)->map(fn($o) => [
                    'value' => $o->id,
                    'label' => $o->translatedValue(),
                ])->toArray()
                : null,
            'is_required' => $attr->pivot->is_required ?? false,
            'multi_locale_input' => $attr->type === 'text',
            'value' => $value,
        ];
    });

    return response()->json($attributes);
}

}