<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Domain\RFQ\Models\RFQ;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'rfq_id','product_name', 'price', 'quantity'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    public function shipments()
{
    return $this->morphMany(OrderItemShipment::class, 'shippable');
}

public function shipment()
{
    return $this->morphOne(OrderItemShipment::class, 'shippable');
}

public function getThumbnailUrlAttribute(): ?string
{
    if (!$this->product) {
        return null;
    }

    $media = $this->product->images
        ->sortBy('sort_order')
        ->firstWhere('is_main', 1)
        ?? $this->product->images
        ->sortBy('sort_order')
        ->first();

    return $media?->url('thumb');
}


}
