<?php

namespace App\Domain\Product\Services;

use App\Models\Product;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProductViewQueryService
{
    public function getProductViewData(string $slug): array
    {
        $user = Auth::user();

        // Загружаем продукт с необходимыми связями, включая варианты
        $product1 = Product::with([
            'images',
            'specifications.translations', // чтобы сразу были переводы ключей/значений
            'priceTiers',
            'supplier',
            'category',
            'variantGroup.items.product',
            'variantGroup.items.product.images',
            'variantGroup.items.media',
    
        ])
        ->where('slug', $slug)
        ->firstOrFail();

        $projects = collect();

        if ($user) {
            $projects = Project::where('buyer_id', $user->id)
                ->where('status', 'draft')
                ->orderByDesc('created_at')
                ->get();
        }

        return compact('product1', 'projects');
    }
}