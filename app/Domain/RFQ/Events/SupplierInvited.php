<?php

namespace App\Domain\RFQ\Events;

use App\Domain\RFQ\Models\RfqParticipant;

class SupplierInvited
{
    public function __construct(
        public RfqParticipant $participant
    ) {}
}