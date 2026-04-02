<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    /**
     * BEFORE SAVE
     */
    public function saving(Category $category)
    {
        // LEVEL

        if ($category->parent_id) {

            $parent = Category::find($category->parent_id);

            $category->level = $parent->level + 1;

        } else {

            $category->level = 0;

        }


        // PATH

        if ($category->parent_id) {

            $parent = Category::find($category->parent_id);

            $category->path =
                $parent->path
                ? $parent->path . '/' . $category->slug
                : $parent->slug . '/' . $category->slug;

        } else {

            $category->path = $category->slug;

        }
    }


    /**
     * AFTER SAVE
     */
    public function saved(Category $category)
    {
        $this->updateChildrenCount($category);

        $this->updateLeafStatus($category);

        if ($category->parent_id) {

            $parent = Category::find($category->parent_id);

            $this->updateChildrenCount($parent);

            $this->updateLeafStatus($parent);

        }
    }


    /**
     * AFTER DELETE
     */
    public function deleted(Category $category)
    {
        if ($category->parent_id) {

            $parent = Category::find($category->parent_id);

            $this->updateChildrenCount($parent);

            $this->updateLeafStatus($parent);

        }
    }


    /**
     * UPDATE CHILDREN COUNT
     */
    private function updateChildrenCount(Category $category)
    {
        $count = Category::where('parent_id', $category->id)->count();

        $category->children_count = $count;

        $category->saveQuietly();
    }


    /**
     * UPDATE LEAF STATUS
     */
    private function updateLeafStatus(Category $category)
    {
        $hasChildren = Category::where(
            'parent_id',
            $category->id
        )->exists();

        $category->is_leaf = !$hasChildren;

        $category->is_selectable = !$hasChildren;

        $category->saveQuietly();
    }
}