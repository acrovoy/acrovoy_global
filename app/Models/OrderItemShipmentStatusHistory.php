<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ShipmentStatus;
use App\Models\User;

class OrderItemShipmentStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_item_shipment_status_history';

    // Разрешённые поля для массового заполнения
    protected $fillable = [
        'shipment_id',
        'status',
        'changed_by',
        'comment',
    ];

    // Преобразуем status к enum автоматически
    protected $casts = [
        'status' => ShipmentStatus::class,
    ];

    /**
     * Связь с shipment
     */
    public function shipment()
    {
        return $this->belongsTo(OrderItemShipment::class, 'shipment_id');
    }

    /**
     * Пользователь, который изменил статус
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    
}
