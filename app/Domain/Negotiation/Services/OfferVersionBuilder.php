<?php

namespace App\Domain\Negotiation\Services;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Models\RfqOfferVersion;

use App\Domain\Negotiation\Models\RfqOfferVersionItem;

class OfferVersionBuilder
{
    public function build(
        RfqOffer $offer,
        array $data
    ): RfqOfferVersion {

        $versionNumber = $offer
            ->versions()
            ->max('version_number') + 1;

        return RfqOfferVersion::create([

            'rfq_offer_id' => $offer->id,

            'version_number' => $versionNumber,

            'total_price' => $data['total_price'] ?? null,

            'comment' => $data['comment'] ?? null,

            'is_counter' => $data['is_counter'] ?? false,

            'created_by' => auth()->id(),
        ]);
    }

    protected function copyItems($offer, $version)
{
    foreach ($offer->items as $item)
    {
        RfqOfferVersionItem::create([

            'offer_version_id' => $version->id,

            'offer_item_id' => $item->id,

            'requirement_id' => $item->requirement_id,

            'unit_price' => $item->unit_price,

            'quantity' => $item->quantity,

            'currency' => $item->currency,

            'lead_time_days' => $item->lead_time_days,

            'moq' => $item->moq,

            'notes' => $item->notes,
        ]);
    }
}

}