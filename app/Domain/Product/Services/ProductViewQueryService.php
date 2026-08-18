<?php

namespace App\Domain\Product\Services;

use App\Models\Product;
use App\Models\Wishlist;
use App\Domain\Project\Models\Project;
use App\Services\Company\ActiveContextService;
use Illuminate\Support\Facades\Auth;

class ProductViewQueryService
{

public function __construct(
        private readonly ActiveContextService $context
    ) {
    }
    public function getProductViewData(string $slug): array
    {
        $buyer = $this->context->buyerProfile();

        /*
        |--------------------------------------------------------------------------
        | WISHLIST
        |--------------------------------------------------------------------------
        */

        $wishlistIds = [];

        if ($buyer) {
            $wishlistIds = Wishlist::query()
                ->where('buyer_type', $buyer::class)
                ->where('buyer_id', $buyer->getKey())
                ->pluck('product_id')
                ->toArray();
        }

        // Загружаем продукт с необходимыми связями, включая варианты
        $product1 = Product::with([
            'images',
            'priceTiers',
            'supplier',
            'category',
            'variantGroup.items.product',
            'variantGroup.items.product.images',
            'variantGroup.items.media',
            'attributeValues.attribute',
            'attributeValues.translations',
            'attributeValues.options.option.translations'

        ])
            ->where('slug', $slug)
            ->firstOrFail();


            $product1->loadMissing([
    'category.attributes',
]);

$attributeOrder = $product1->category
    ? $product1->category->attributes
        ->pluck('pivot.sort_order', 'id')
    : collect();

$product1->setRelation(
    'attributeValues',
    $product1->attributeValues
        ->sortBy(function ($attrValue) use ($attributeOrder) {
            return $attributeOrder->get(
                $attrValue->attribute_id,
                PHP_INT_MAX
            );
        })
        ->values()
);



        $projects = collect();

        if ($buyer) {
            $projects = Project::query()
                ->where('buyer_type', $buyer::class)
                ->where('buyer_id', $buyer->getKey())
                ->where('status', 'draft')
                ->orderByDesc('created_at')
                ->get();
        }


        $gallery = [];

        foreach ($product1->thumbnails as $media) {

            $src = $media['large'];
            $thumb = $media['thumb'] ?? $src;

            $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'])) {
                $type = 'image';
            } elseif (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
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


        $shippingTemplates = $product1->shippingTemplates->map(function ($template) use ($product1) {
    $template->computed_price = $product1->computeShippingPrice($template);
    return $template;
});



        return compact('product1', 'projects', 'gallery', 'shippingTemplates', 'wishlistIds');
    }
}
