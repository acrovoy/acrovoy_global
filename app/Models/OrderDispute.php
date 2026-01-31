<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'action',
        'attachment',
        'status',
        'admin_comment',
        'buyer_comment',
        'supplier_comment'
    ];

    // Связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Связь с заказом
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

