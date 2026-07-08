<?php

namespace App\Domain\RFQ\Models;

use App\Domain\RFQ\Enums\RfqStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\RFQ\Models\RfqAttributeValue;
use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\RFQ\Enums\RfqVisibilityType;
use App\Domain\Negotiation\Models\RfqOfferVersion;

use App\Services\Company\ActiveContextService;

use App\Models\ShippingDimensions;
use App\Models\ShippingTemplate;
use App\Models\User;
use App\Models\Category;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\Product;

class Rfq extends Model
{
    use SoftDeletes;
    

    protected $table = 'rfqs';

    protected $fillable = [
        'buyer_id',
        'buyer_type',
        'created_by',
        'title',
        'description',
        'customization',
        'product_id',
        'delivery_address_id',
        'type',
        'status',
        'published_at',
        'closed_at',
        'category_id',
        'visibility_type',
        
    ];

    protected $casts = [
        'status' => RfqStatus::class,
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
        'visibility_type' => RfqVisibilityType::class,
        'customization' => 'boolean',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
    public function participants()
    {
        return $this->hasMany(RfqParticipant::class);
    }

    public function participant()
{
    return $this->morphTo();
}

    public function offers()
{
    return $this->hasMany(
        RfqOffer::class
    );
}

public function attributeValues()
{
    return $this->hasMany(RfqAttributeValue::class)
        ->with([
            'attribute.translations',
            'attribute.options.translations',
        ]);
}

/*
|--------------------------------------------------------------------------
| CUSTOM ATTRIBUTE VALUES
|--------------------------------------------------------------------------
*/

public function customAttributeValues()
{
    return $this->attributeValues()
        ->whereHas('attribute', function ($q) {
            $q->where('is_custom', true);
        });
}

/*
|--------------------------------------------------------------------------
| SYSTEM ATTRIBUTE VALUES
|--------------------------------------------------------------------------
*/

public function systemAttributeValues()
{
    return $this->attributeValues()
        ->whereHas('attribute', function ($q) {
            $q->where('is_custom', false);
        });
}


public function isPrivate(): bool
{
    return $this->visibility_type === RfqVisibilityType::PRIVATE;
}

public function isCategory(): bool
{
    return $this->visibility_type === RfqVisibilityType::CATEGORY;
}

public function isPlatform(): bool
{
    return $this->visibility_type === RfqVisibilityType::PLATFORM;
}

public function isOpen(): bool
{
    return $this->visibility_type === RfqVisibilityType::OPEN;
}
    

public function visibilityCategories()
{
    return $this->belongsToMany(
        Category::class,
        'rfq_visibility_categories'
    );
}

/**
 * Get human-readable public RFQ identifier.
 *
 * Example:
 * ID: 12 → RFQ-00012
 *
 * This is used only for UI/display purposes.
 * DO NOT use for database relations or logic.
 *
 * @return string
 */
public function getPublicIdAttribute()
{
    return 'RFQ-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
}


public function hiddenAttributes()
{
    return $this->belongsToMany(
        \App\Models\Attribute::class,
        'rfq_hidden_attributes',
        'rfq_id',
        'attribute_id'
    );
}

public function deliveryAddress()
{
    return $this->belongsTo(UserAddress::class, 'delivery_address_id');
}

public function isPublished(): bool
{
    return $this->status === RfqStatus::PUBLISHED;
}

public function isLocked(): bool
{
    return $this->status !== RfqStatus::DRAFT;
}

public function category()
{
    return $this->belongsTo(\App\Models\Category::class);
}


public function computeShippingPrice(ShippingTemplate $template): float
    {
        $finalPrice = $template->price;

        if ($this->shippingDimensions) {
            $dimensions = $this->shippingDimensions;

            switch ($template->price_unit) {
                case 'per_kg':
                    $finalPrice = $template->price * $dimensions->weight;
                    break;

                case 'per_cubic_meter':
                    $volume = ($dimensions->length / 100) * ($dimensions->width / 100) * ($dimensions->height / 100);
                    $finalPrice = $template->price * $volume;
                    break;

                case 'per_item':
                default:
                    $finalPrice = $template->price;
            }
        }

        return round($finalPrice, 2);
    }

    public function shippingDimensions()
{
    return $this->morphMany(
        ShippingDimensions::class,
        'dimensionable'
    );
}

public function currentShippingDimensions()
{
    $context = app(ActiveContextService::class);

    return $this->shippingDimensions()
        ->where('supplier_type', $context->type())
        ->where('supplier_id', $context->id())
        ->first();
}

public function getAcceptedOfferVersionAttribute(): ?RfqOfferVersion
{
    return RfqOfferVersion::query()
        ->whereHas('offer', function ($query) {
            $query->where('rfq_id', $this->id);
        })
        ->where('status', 'accepted')
        ->first();
}


public function getOrderIdAttribute(): ?int
{
    return Order::where(
        'offer_version_id',
        $this->accepted_offer_version?->id
    )->value('id');
}

public function product()
{
    return $this->belongsTo(Product::class);
}

}