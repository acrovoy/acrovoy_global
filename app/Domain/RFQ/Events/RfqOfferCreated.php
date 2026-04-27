<?php

namespace App\Domain\RFQ\Events;

use App\Domain\RFQ\Models\RfqOffer;

class RfqOfferCreated
{
    public function __construct(
        public RfqOffer $offer
    ) {}
}