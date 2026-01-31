<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierReputationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'score_change',
        'reason',
        'order_id',
    ];

    /**
     * Связь с поставщиком
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Связь с заказом (опционально)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

