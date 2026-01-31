<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Supplier;

class SupplierOrderAccessService
{
    public function canAccess(Order $order, Supplier $supplier): bool
    {
        // ✅ Заказ из RFQ
        if ($order->rfqOffer) {
            return $order->rfqOffer->supplier_id === $supplier->id;
        }

        // ✅ Обычный заказ из каталога
        return $order->items()
            ->whereHas('product', fn ($q) =>
                $q->where('supplier_id', $supplier->id)
            )
            ->exists();
    }
}