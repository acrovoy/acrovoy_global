<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'price',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute()
    {
        $product = $this->product;

        if (!$product) {
            return asset('images/no-image.png');
        }

        $mainImage = $product->images
            ->firstWhere('is_main', 1)
            ?? $product->images->first();

        return $mainImage?->cdn_url ?? $product->image_url ?? asset('images/no-image.png');
    }

}
