<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    protected static array $allowedTransitions = [
    'pending'     => ['confirmed', 'cancelled'],
    'confirmed'   => ['paid', 'cancelled'],
    'paid'        => ['processing', 'cancelled'],
    'processing'  => ['production', 'shipped', 'cancelled'],
    'production'  => ['shipped', 'cancelled'],
    'shipped'     => ['delivered'],
    'delivered'   => ['completed'],
    'completed'   => [],
    'cancelled'   => [],
];

    public static function change(
        Order $order,
        string $newStatus,
        ?string $comment = null
    ): void {
        $current = $order->status;

        if ($current === $newStatus) {
            return;
        }

        if (!in_array($newStatus, self::$allowedTransitions[$current] ?? [])) {
            throw ValidationException::withMessages([
                'status' => "Нельзя изменить статус с {$current} на {$newStatus}",
            ]);
        }

        $order->update([
            'status' => $newStatus,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'comment' => $comment,
        ]);
    }

    public static function availableStatuses(string $current): array
    {
        return self::$allowedTransitions[$current] ?? [];
    }
}
