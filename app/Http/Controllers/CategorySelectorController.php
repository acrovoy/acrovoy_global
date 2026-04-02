<?php

namespace App\Http\Controllers;

use App\Models\Category;

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

}