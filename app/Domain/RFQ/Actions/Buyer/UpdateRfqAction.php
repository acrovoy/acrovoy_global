<?php

namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Enums\RfqStatus;
use App\Domain\RFQ\DTO\UpdateRfqData;

class UpdateRfqAction
{
    public function execute(Rfq $rfq, UpdateRfqData $data): Rfq
    {
        $payload = [
            'title' => $data->title,
            'description' => $data->description,
            'closed_at' => $data->closed_at ?? null,
            'delivery_address_id' => $data->delivery_address_id ?? null,
        ];

        // 🔒 lock logic
        if ($rfq->status !== RfqStatus::DRAFT) {
            unset(
                $payload['title'],
                $payload['description'],
                $payload['closed_at'],
                $payload['delivery_address_id']
            );
        }

        $rfq->update($payload);

        return $rfq;
    }
}