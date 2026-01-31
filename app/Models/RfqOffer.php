<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqOffer extends Model
{
    protected $fillable = [
        'rfq_id',
        'supplier_id',
        'price',
        'delivery_days',
        'comment',
        'status',
        'buyer_viewed_at',
        'supplier_viewed_at',
        'shipping_template_id',
    ];

    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    // Связь на пользователя — поставщика
    public function supplier()
{
    return $this->belongsTo(Supplier::class, 'supplier_id');
}

    public function shipping_template()
{
    return $this->belongsTo(ShippingTemplate::class);
}


public function order()
    {
        return $this->hasOne(Order::class, 'rfq_offer_id', 'id');
    }

  

    
}

