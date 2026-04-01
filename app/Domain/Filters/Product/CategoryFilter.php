<?php

namespace App\Domain\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;
use App\Models\Category;

class CategoryFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value): Builder
    {

  


        if (!$value) {
            return $query;
        }

        $category = Category::with('children.children')
            ->where('slug', $value)
            ->first();

        if (!$category) {
            return $query;
        }

        $ids = $this->collectIds($category);

        return $query->whereIn('category_id', $ids);
    }

    private function collectIds(Category $category): array
    {
        $ids = [$category->id];

        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->collectIds($child));
        }

        return $ids;
    }
}