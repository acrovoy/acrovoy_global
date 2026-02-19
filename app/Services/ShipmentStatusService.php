<?php

namespace App\Services;

use App\Models\OrderItemShipment;
use App\Models\OrderItemShipmentStatusHistory;
use App\Enums\ShipmentStatus;
use Illuminate\Validation\ValidationException;
use App\Events\ShipmentStatusChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ShipmentStatusService
{
    protected static array $allowedTransitions = [
        'pending'        => ['accepted', 'cancelled'],
        'accepted'       => ['picked_up', 'cancelled'],
        'picked_up'      => ['in_transit'],
        'in_transit'     => ['arrived_at_destination', 'delivered'],
        'arrived_at_destination' => ['delivered'],
        'delivered'      => ['completed'],
        'completed'      => [],
        'cancelled'      => [],
    ];

    public static function change(
    OrderItemShipment $shipment,
    ShipmentStatus|string $newStatus,
    ?string $comment = null
): void {

    $current = $shipment->status;

    $newStatus = $newStatus instanceof ShipmentStatus
        ? $newStatus->value
        : $newStatus;

    if ($current === $newStatus) {
        return;
    }

    if (!in_array($newStatus, self::$allowedTransitions[$current] ?? [])) {
        throw ValidationException::withMessages([
            'status' => "Нельзя изменить статус с {$current} на {$newStatus}",
        ]);
    }

    $userId = Auth::id();

    DB::transaction(function () use ($shipment, $newStatus, $comment, $userId) {

        $shipment->update([
            'status' => $newStatus,
            'changed_by' => $userId,
        ]);

       

\Log::debug('Creating shipment history', [
    'shipment_id' => $shipment->id,
    'status' => $newStatus,
    'changed_by' => Auth::id(),
    'comment' => $comment,
]);

        OrderItemShipmentStatusHistory::create([
            'shipment_id' => $shipment->id,
            'status' => $newStatus,
            'changed_by' => $userId,
            'comment' => $comment,
        ]);

        event(new ShipmentStatusChanged($shipment, $newStatus));
    });
}
    public static function availableStatuses(string $current): array
    {
        return self::$allowedTransitions[$current] ?? [];
    }
}
