<?php

namespace App\Domain\Shipping\Models;

use App\Models\ShippingTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingTemplateDeliveryType extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_template_id',
        'delivery_type_id',
        'price',
        'price_unit',
        'delivery_time',
        'incoterm',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function shippingTemplate()
    {
        return $this->belongsTo(
            ShippingTemplate::class,
            'shipping_template_id'
        );
    }

    public function deliveryType()
    {
        return $this->belongsTo(
            DeliveryType::class,
            'delivery_type_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getPriceUnitLabelAttribute(): string
    {
        return match ($this->price_unit) {
            'per_item' => 'per item',
            'per_kg' => 'per kg',
            'per_cubic_meter' => 'per m³',
            'flat' => 'flat rate',
            default => $this->price_unit,
        };
    }

    public function getDeliveryTypeNameAttribute(): string
    {
        return $this->deliveryType?->name
            ?? $this->deliveryType?->code
            ?? '';
    }
}