<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Services\OfferVersionBuilder;

class CreateCounterOfferVersionAction
{
    public function execute(
        RfqOffer $offer,
        array $data
    ) {

        $data['is_counter'] = true;

        return app(OfferVersionBuilder::class)
            ->build($offer, $data);
    }
}