<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierReview extends Model
{
    protected $fillable = [
        'supplier_id',
        'order_id',
        'user_id',
        'rating',
        'comment',
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
