<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\OrderItemShipment;

class ShipmentStatusChanged
{
    use SerializesModels;

    public OrderItemShipment $shipment;
    public string $newStatus;

    public function __construct(OrderItemShipment $shipment, string $newStatus)
    {
        $this->shipment = $shipment;
        $this->newStatus = $newStatus;
    }
}
