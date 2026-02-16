<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'status',
    'type',
    'total',
    'delivery_price',
    'delivery_method',
    'notes',
    'first_name',
    'last_name',
    'country',
    'city',
    'region',
    'street',
    'postal_code',
    'phone',
    'tracking_number',
    'invoice_file',
    'invoice_delivery_file',
    'rfq_offer_id',
    'project_id',
];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusHistory()
{
    return $this->hasMany(OrderStatusHistory::class)
                ->orderBy('created_at');
}

public function reviews()
{
    return $this->hasMany(Review::class);
}

// Споры по заказу
public function disputes()
{
    return $this->hasMany(OrderDispute::class);
}

public function rfqOffer()
{
    return $this->belongsTo(RfqOffer::class, 'rfq_offer_id', 'id');
}

public function shipments()
{
    return $this->hasMany(\App\Models\OrderItemShipment::class);
}

public function countryRelation()
{
    return $this->belongsTo(\App\Models\Country::class, 'country');
}

public function regionRelation()
{
    return $this->belongsTo(\App\Models\Location::class, 'region');
}

public function cityRelation()
{
    return $this->belongsTo(\App\Models\Location::class, 'city_id');
}


}
