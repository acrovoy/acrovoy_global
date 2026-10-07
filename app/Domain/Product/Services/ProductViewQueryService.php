<?php

namespace App\Domain\Product\Services;

use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Unit;
use App\Domain\Project\Models\Project;
use App\Services\Company\ActiveContextService;

class ProductViewQueryService
{
    public function __construct(
        private readonly ActiveContextService $context
    ) {}

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


        /*
        |--------------------------------------------------------------------------
        | PRODUCT
        |--------------------------------------------------------------------------
        */

        $product1 = Product::with([
            'images',
            'priceTiers',
            'supplier',
            'category',

            'variantGroup.items.product',
            'variantGroup.items.product.images',
            'variantGroup.items.media',

            /*
             * Product attribute values
             */
            'attributeValues.attribute.translations',
            'attributeValues.attribute.group.translations',
            'attributeValues.attribute.unit.translations',
            'attributeValues.translations',
            'attributeValues.options.option.translations',

            /*
             * Category attributes
             */
            'category.attributes',
        ])
            ->where('slug', $slug)
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | LOAD CATEGORY ATTRIBUTES
        |--------------------------------------------------------------------------
        */

        $product1->loadMissing([
            'category.attributes',
        ]);


        /*
        |--------------------------------------------------------------------------
        | LOAD UNITS MANUALLY
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | We do NOT rely on:
        |
        |     $attribute->unit
        |
        | because in this particular query Eloquent may already have
        | an Attribute instance with a cached null relation.
        |
        | The source of truth is:
        |
        |     attributes.unit_id
        |
        | Therefore:
        |
        | 1. collect all unit_id values
        | 2. load Units directly
        | 3. attach Unit to the Attribute relation manually
        |
        |--------------------------------------------------------------------------
        */

        $unitIds = $product1->attributeValues
            ->map(fn($attributeValue) => $attributeValue->attribute?->unit_id)
            ->filter()
            ->unique()
            ->values();


        $units = Unit::query()
            ->with([
                'translations',
                'translation',
            ])
            ->whereIn('id', $unitIds)
            ->get()
            ->keyBy('id');


        /*
        |--------------------------------------------------------------------------
        | ATTACH UNITS TO PRODUCT ATTRIBUTES
        |--------------------------------------------------------------------------
        */

        $product1->attributeValues->each(
            function ($attributeValue) use ($units) {

                $attribute = $attributeValue->attribute;

                if (!$attribute) {
                    return;
                }

                $unitId = $attribute->unit_id;

                if (!$unitId) {
                    return;
                }

                $unit = $units->get($unitId);

                /*
                 * Manually set the Eloquent relation.
                 *
                 * After this:
                 *
                 * $attribute->unit
                 *
                 * will return the Unit model.
                 */

                $attribute->setRelation('unit', $unit);
            }
        );


        /*
        |--------------------------------------------------------------------------
        | ATTRIBUTE ORDER
        |--------------------------------------------------------------------------
        */

        $attributeOrder = $product1->category
            ? $product1->category->attributes
            ->pluck('pivot.sort_order', 'id')
            : collect();


        /*
|--------------------------------------------------------------------------
| SORT PRODUCT ATTRIBUTES
|--------------------------------------------------------------------------
|
| Order:
|
| 1. Attribute Group sort_order
| 2. Attribute sort_order inside category
|
|--------------------------------------------------------------------------
*/

        $product1->setRelation(
            'attributeValues',
            $product1->attributeValues
                ->sortBy(function ($attrValue) use ($attributeOrder) {

                    $attribute = $attrValue->attribute;

                    return [
                        // GROUP ORDER
                        $attribute?->group?->sort_order ?? PHP_INT_MAX,

                        // ATTRIBUTE ORDER
                        $attributeOrder->get(
                            $attrValue->attribute_id,
                            PHP_INT_MAX
                        ),
                    ];
                })
                ->values()
        );


        /*
        |--------------------------------------------------------------------------
        | PROJECTS
        |--------------------------------------------------------------------------
        */

        $projects = collect();

        if ($buyer) {

            $projects = Project::query()
                ->where('buyer_type', $buyer::class)
                ->where('buyer_id', $buyer->getKey())
                ->where('status', 'draft')
                ->orderByDesc('created_at')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | GALLERY
        |--------------------------------------------------------------------------
        */

        $gallery = [];

        foreach ($product1->thumbnails as $media) {

            $src = $media['large'];
            $thumb = $media['thumb'] ?? $src;

            $ext = strtolower(
                pathinfo($src, PATHINFO_EXTENSION)
            );

            if (in_array($ext, [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'webp',
                'avif',
            ])) {

                $type = 'image';
            } elseif (in_array($ext, [
                'mp4',
                'webm',
                'ogg',
                'mov',
            ])) {

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


        /*
|--------------------------------------------------------------------------
| SHIPPING TEMPLATES
|--------------------------------------------------------------------------
*/

$shippingTemplates = $product1->shippingTemplates
    ->filter(fn ($template) => $template->is_active)
    ->map(function ($template) use ($product1) {

        $template->computed_price =
            $product1->computeShippingPrice($template);

        return $template;
    });


       /*
|--------------------------------------------------------------------------
| SHIPPING OPTIONS FOR PRODUCT CARD
|--------------------------------------------------------------------------
*/

$deliveryOptions = $shippingTemplates
    ->map(function ($template) use ($product1) {

        $option = $template->deliveryOptions
            ->filter(function ($option) {
                return $option->is_active
                    && (float) $option->price > 0;
            })
            ->sortBy('sort_order')
            ->first();

        if (!$option) {
            return null;
        }

        $computedPrice = $product1->computeShippingPrice(
            $template,
            $option
        );

        if ($computedPrice <= 0) {
            return null;
        }

        $deliveryTypeName =
            $option->deliveryType?->name
            ?? $option->deliveryType?->code
            ?? '';

        return [
            'title' => trim(
                $template->title .
                ($deliveryTypeName ? ' · ' . $deliveryTypeName : '')
            ),
            'price' => '$' . number_format($computedPrice, 2),
            'description' => $template->description,
            'badge' => $template->delivery_time
                ? $template->delivery_time . ' days'
                : null,
            'expanded' => false,
            'template' => $template,
            'delivery_option' => $option,
        ];
    })
    ->filter()
    ->values();

/*
|--------------------------------------------------------------------------
| SHOW MORE
|--------------------------------------------------------------------------
*/

$deliveryOptions = $deliveryOptions
    ->map(function ($option, $index) {
        $option['expanded'] = $index < 2;
        return $option;
    });

$hasHiddenDelivery = $deliveryOptions->count() > 2;




        $reviewsCount = $product1->reviews->count();
        $rating = $reviewsCount > 0 ? round($product1->reviews->avg('rating'), 1) : 0;
        $soldCount = $product1->orders->where('status', 'completed')->sum('quantity');
        $inWishlist = in_array($product1->id, $wishlistIds);

        $measurementAttributes = $product1->attributeValues
            ->filter(function ($attrValue) {

                $value = $attrValue->display_value;

                return $attrValue->attribute?->type === 'measurement'
                    && filled($value)
                    && (float) $value > 0;
            })
            ->values();


         $customAbilityAttributes = $product1->attributeValues
        ->load([
            'attribute',
            'options.option.translations',
        ])
        ->filter(function ($attrValue) {
            return $attrValue->attribute?->group_id === 29;
        })
        ->values();


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return compact(
            'product1',
            'projects',
            'gallery',
            'shippingTemplates',
            'wishlistIds',
            'rating',
            'soldCount',
            'inWishlist',
            'reviewsCount',
            'measurementAttributes',
            'customAbilityAttributes',
            'deliveryOptions',
            'hasHiddenDelivery',
            
        );
    }
}
