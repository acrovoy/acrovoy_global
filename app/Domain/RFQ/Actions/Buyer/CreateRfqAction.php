<?php

namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\DTO\CreateRfqData;
use App\Domain\RFQ\Enums\RfqStatus;
use App\Domain\RFQ\Models\Rfq;

class CreateRfqAction
{
    public function execute(
        CreateRfqData $data,
        int $buyerId,
        string $buyerType,
        int $createdBy
    ): Rfq {
        return Rfq::create([
            'buyer_type' => $buyerType,
            'buyer_id'   => $buyerId,

            'created_by' => $createdBy,

            'title'       => $data->title,
            'description' => $data->description,
            'type'        => $data->type,

            'status'    => RfqStatus::DRAFT,
            'closed_at' => $data->closed_at,
        ]);
    }
}