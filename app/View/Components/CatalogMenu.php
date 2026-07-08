<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Category;

class CatalogMenu extends Component
{
    public $catalogColumns;

    public function __construct()
    {
        // Берем верхнеуровневые категории с детьми
        $categories = Category::visible()
            ->whereNull('parent_id')
            ->with([
                'children' => fn($q) => $q->visible()->orderBy('sort_order'),
                'children.children' => fn($q) => $q->visible()->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get();

        // Разбиваем коллекцию на 4 колонки
        $this->catalogColumns = $categories->chunk(ceil($categories->count() / 4));
    }

    public function render()
    {
        return view('components.catalog-menu');
    }
}
