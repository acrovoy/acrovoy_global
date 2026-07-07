<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDimensions extends Model
{
    use HasFactory;

    protected $table = 'shipping_dimensions';

    protected $fillable = [
        'dimensionable_type',
        'dimensionable_id',
        'length',
        'width',
        'height',
        'weight',
        'package_type',
        'supplier_type',
        'supplier_id',
    ];

    /**
     * Связь с продуктом (обратная)
     */
    
    public function dimensionable()
{
    return $this->morphTo();
}
}