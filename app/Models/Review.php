<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order_id',
        'user_id',
        'rating',
        'match_rating',
        'comment',
    ];

    // Связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Связь с продуктом
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Связь с заказом
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    
}
