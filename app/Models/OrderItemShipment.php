<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
