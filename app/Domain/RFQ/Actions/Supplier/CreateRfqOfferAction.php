<?php

namespace App\Domain\RFQ\Actions\Supplier;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Models\RfqOfferItem;
use App\Domain\RFQ\DTO\CreateRfqOfferData;
use App\Domain\Negotiation\Services\NegotiationAuditService;

class CreateRfqOfferAction
{
    public function __construct(
        private NegotiationAuditService $audit
    ) {}

    public function execute(
        CreateRfqOfferData $data,
        int $supplierId,
        int $userId
    ): RfqOffer {
        $offer = RfqOffer::create([
            'rfq_id' => $data->rfq_id,
            'supplier_id' => $supplierId,
            'status' => 'draft',
        ]);

        $total = 0;

        foreach ($data->items as $item) {
            RfqOfferItem::create([
                'offer_id' => $offer->id,
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'price' => $item['price'],
                'quantity' => $item['quantity'] ?? 1,
            ]);

            $total += $item['price'] * ($item['quantity'] ?? 1);
        }

        $offer->update([
            'total_amount' => $total,
        ]);

        // 🧠 AUDIT LOG
        $this->audit->log(
            'offer.created',
            $offer,
            $offer->rfq_id,
            $userId,
            [
                'total' => $total
            ]
        );

        return $offer;
    }
}