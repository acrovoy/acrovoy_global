<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'buyer_type',
        'buyer_id',
        'created_by',

    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * WHO created wishlist item
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
