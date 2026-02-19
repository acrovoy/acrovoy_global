<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\OrderItemShipmentStatusHistory;

class OrderItemShipment extends Model
{
    protected $table = 'order_item_shipments';

    protected $fillable = [
        'order_id',
        'shippable_type',
        'shippable_id',
        'weight',
        'length',
        'width',
        'height',
        'shipping_price',
        'delivery_time',
        'status',
        'tracking_number',

        // ===== Provider =====
        'provider_type',
        'provider_id',

        // ===== Origin (погрузка) =====
        'origin_country_id',
        'origin_region_id',
        'origin_city_id',
        'origin_address',
        'origin_contact_name',
        'origin_contact_phone',

        // ===== Destination (выгрузка) =====
        'destination_country_id',
        'destination_region_id',
        'destination_city_id',
        'destination_address',
        'destination_contact_name',
        'destination_contact_phone',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'shipping_price' => 'decimal:2',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function provider()
    {
        return $this->morphTo();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function shippable()
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function orderItem()
{
    return $this->belongsTo(OrderItem::class, 'order_item_id');
}

public function destinationCountry()
    {
        return $this->belongsTo(\App\Models\Country::class, 'destination_country_id');
    }

    public function destinationRegion()
    {
        return $this->belongsTo(\App\Models\Location::class, 'destination_region_id');
    }

    public function destinationCity()
    {
        return $this->belongsTo(\App\Models\Location::class, 'destination_city_id');
    }

    public function originCountry()
    {
        return $this->belongsTo(\App\Models\Country::class, 'origin_country_id');
    }

    public function originRegion()
    {
        return $this->belongsTo(\App\Models\Location::class, 'origin_region_id');
    }

    public function originCity()
    {
        return $this->belongsTo(\App\Models\Location::class, 'origin_city_id');
    }

    public function statuses()
    {
        return $this->hasMany(OrderItemShipmentStatusHistory::class, 'shipment_id', 'id')
                    ->orderBy('created_at', 'asc'); // чтобы в хронологическом порядке
    }
    
}
