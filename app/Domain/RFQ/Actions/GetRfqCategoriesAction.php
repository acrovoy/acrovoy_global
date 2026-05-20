<?php

namespace App\Domain\Rfq\Actions;

use App\Models\Category;

class GetRfqCategoriesAction
{
    /**
     * Return selectable RFQ categories with translations
     */
    public function execute()
    {
        return Category::query()
            ->with('translations')
            ->selectable()
            ->forType('rfq')
            ->ordered()
            ->get();
    }
}