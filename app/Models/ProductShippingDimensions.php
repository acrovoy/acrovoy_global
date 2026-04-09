<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductShippingDimensions extends Model
{
    use HasFactory;

    protected $table = 'product_shipping_dimensions';

    protected $fillable = [
        'product_id',
        'length',
        'width',
        'height',
        'weight',
        'package_type',
    ];

    /**
     * Связь с продуктом (обратная)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}