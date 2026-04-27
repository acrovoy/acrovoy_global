<?php

namespace App\Domain\RFQ\Events;

use App\Domain\RFQ\Models\Rfq;

class RfqCreated
{
    public function __construct(
        public readonly Rfq $rfq
    ) {}
}