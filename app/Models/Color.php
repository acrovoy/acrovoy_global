<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color',
        'texture',
        'texture_path',
        'linked_product_id',
    ];

    /**
     * Связь с продуктом
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Связанный продукт (для linked_product_id)
     */
    public function linkedProduct()
    {
        return $this->belongsTo(Product::class, 'linked_product_id');
    }

  

}
