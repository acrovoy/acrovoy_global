<?php

namespace App\Domain\Product\Services;

use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Unit;
use App\Domain\Project\Models\Project;
use App\Services\Company\ActiveContextService;

use App\Domain\Returns\Models\ProductReturnPolicy;
use App\Domain\Returns\Models\ReturnPolicy;
use App\Domain\Returns\Models\SupplierReturnPolicySetting;




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

            'paymentMethods.translations',
            'paymentTerms.translations',

            'returnPolicy',
            'returnPolicy.returnPolicy.translations',
            'returnPolicy.returnPolicy.reasons.translations',
            'returnPolicy.returnPolicy.resolutions.translations',

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
    ->filter(fn($template) => $template->is_active)
    ->map(function ($template) use ($product1) {

        // Загружаем курьера и его переводы.
        $template->loadMissing('courier.translations');

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
            ->where('is_active', true)
            ->sortBy('sort_order')
            ->first();

        $computedPrice = null;
        $price = null;
        $deliveryTypeName = '';
        $deliveryTime = null;

        if ($option) {
            if ($option->price !== null && $option->price !== '') {
                $computedPrice = $product1->computeShippingPrice(
                    $template,
                    $option
                );

                if ($computedPrice > 0) {
                    $price = '$' . number_format($computedPrice, 2);
                }
            }

            $deliveryTypeName =
                $option->deliveryType?->name
                ?? $option->deliveryType?->code
                ?? '';

            $deliveryTime = $option->delivery_time;
        }

        $courier = $template->courier;

        $courierTranslation = $courier?->translations
            ->firstWhere('locale', app()->getLocale())
            ?? $courier?->translations->firstWhere('locale', 'en');

        return [
            'title' => trim(
                $template->title .
                ($deliveryTypeName ? ' · ' . $deliveryTypeName : '')
            ),
            'price' => $price,
            'description' => $template->description,
            'badge' => $deliveryTime
                ? $deliveryTime . ' days'
                : null,
            'expanded' => false,
            'template' => $template,
            'delivery_option' => $option,

            // Courier.
            'courier_logo' => $courier?->logo,
            'courier_name' => $courierTranslation?->name
                ?? $courier?->code,
        ];
    })
    ->values();


/*
|--------------------------------------------------------------------------
| SHOW MORE
|--------------------------------------------------------------------------
*/

$deliveryOptions = $deliveryOptions
    ->map(function ($option, $index) {
        $option['expanded'] = $index < 4;
        return $option;
    });

$hasHiddenDelivery = $deliveryOptions->count() > 4;




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
        | RETURN POLICY
        |--------------------------------------------------------------------------
        |
        | The product can either:
        |
        | 1. Use the supplier default policy
        | 2. Use a specific policy assigned to the product
        |
        | If the product uses supplier default, the actual policy is resolved
        | through supplier_return_policy_settings.
        |
        |--------------------------------------------------------------------------
        */

        $productReturnPolicy = $product1->returnPolicy;

        $returnPolicy = null;

        if ($productReturnPolicy) {

            /*
     * ---------------------------------------------------------
     * SPECIFIC PRODUCT POLICY
     * ---------------------------------------------------------
     */

            if (
                !$productReturnPolicy->use_supplier_default
                && $productReturnPolicy->returnPolicy
            ) {

                $returnPolicy = $productReturnPolicy->returnPolicy;
            }


            /*
     * ---------------------------------------------------------
     * SUPPLIER DEFAULT POLICY
     * ---------------------------------------------------------
     */ elseif (
                $productReturnPolicy->use_supplier_default
                && $product1->supplier
            ) {

                $supplierDefaultPolicyId =
                    SupplierReturnPolicySetting::query()
                    ->where(
                        'supplier_id',
                        $product1->supplier->id
                    )
                    ->value('default_return_policy_id');


                if ($supplierDefaultPolicyId) {

                    $returnPolicy = ReturnPolicy::query()
                        ->with([
                            'translations',
                            'reasons.translations',
                            'resolutions.translations',
                        ])
                        ->where('id', $supplierDefaultPolicyId)
                        ->where('is_active', true)
                        ->first();
                }
            }
        }


        /*
|--------------------------------------------------------------------------
| RETURN POLICY TRANSLATION
|--------------------------------------------------------------------------
*/

        $returnPolicyTranslation = null;

        if ($returnPolicy) {

            $returnPolicyTranslation =
                $returnPolicy->translation(app()->getLocale())
                ?? $returnPolicy->translation('en');
        }


        /*
|--------------------------------------------------------------------------
| RETURN POLICY DISPLAY DATA
|--------------------------------------------------------------------------
*/

        $returnPolicyData = null;

        if ($returnPolicy) {

            $returnPolicyName =
                $returnPolicyTranslation?->name
                ?: $returnPolicy->name;


            $returnPolicyDescription =
                $returnPolicyTranslation?->description
                ?: null;


            $returnShippingPayer = match ($returnPolicy->return_shipping_payer) {
                'buyer' => 'Оплачивает покупатель',
                'supplier' => 'Supplier pays return shipping',
                'depends_on_reason' => 'Return shipping depends on reason',
                default => null,
            };


            $returnPolicyData = [
                'name' => $returnPolicyName,
                'description' => $returnPolicyDescription,

                'return_window_days' =>
                $returnPolicy->return_window_days,

                'return_shipping_payer' =>
                $returnShippingPayer,

                'restocking_fee_enabled' =>
                $returnPolicy->restocking_fee_enabled,

                'restocking_fee_percent' =>
                $returnPolicy->restocking_fee_percent,

                'custom_products_returnable' =>
                $returnPolicy->custom_products_returnable,
            ];
        }






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
            'returnPolicy', 
            'returnPolicyData',

        );
    }
}
