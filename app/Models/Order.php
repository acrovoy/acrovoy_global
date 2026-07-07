<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Models\RfqOfferVersion;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'buyer_type',
    'buyer_id',
    'created_by',
    'status',
    'type',
    'total',
    'delivery_price',
    'delivery_method',
    'shipping_template_id',
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
    'offer_version_id',
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
    return $this->belongsTo(RfqOffer::class, 'offer_version_id'); 
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

public function shippingTemplate()
{
    return $this->belongsTo(ShippingTemplate::class);
}

public function offerVersion()
{
    return $this->belongsTo(RfqOfferVersion::class, 'offer_version_id');
}

}
