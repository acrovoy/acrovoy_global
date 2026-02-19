<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case Pending               = 'pending';
    case Accepted              = 'accepted';
    case PickedUp              = 'picked_up';
    case InTransit             = 'in_transit';
    case ArrivedAtDestination  = 'arrived_at_destination';
    case Delivered             = 'delivered';
    case Completed             = 'completed';
    case Cancelled             = 'cancelled';
}
