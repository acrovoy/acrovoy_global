<?php

namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\Models\Rfq;

use App\Domain\RFQ\DTO\UpdateRfqData;

class UpdateRfqAction
{
    public function execute(Rfq $rfq, UpdateRfqData $data): Rfq
    {
        $rfq->update([
            'title' => $data->title,
            'description' => $data->description,
        ]);

        return $rfq;
    }
}