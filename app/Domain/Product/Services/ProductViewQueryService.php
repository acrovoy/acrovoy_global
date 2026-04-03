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
            'attributeValues.attribute',
            'attributeValues.translations',
            'attributeValues.options.option', // чтобы подтянуть саму Option и её переводы
    
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

        $gallery = [];

            foreach ($product1->thumbnails as $media) {

                $src = $media['large'];
                $thumb = $media['thumb'] ?? $src;

                $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg','jpeg','png','gif','webp','avif'])) {
                    $type = 'image';
                } elseif (in_array($ext, ['mp4','webm','ogg','mov'])) {
                    $type = 'video';
                } elseif ($ext === 'pdf') {
                    $type = 'pdf';
                } else {
                    $type = 'file';
                }

                $gallery[] = [
                    'type' => $type,
                    'src' => $src,
                    'thumb' => $thumb,
                ];
            }

        return compact('product1', 'projects', 'gallery');
    }
}