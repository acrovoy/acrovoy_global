<?php

namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\Models\RfqParticipant;
use App\Domain\RFQ\Enums\RfqParticipantStatus;
use App\Domain\RFQ\DTO\InviteSupplierData;
use Carbon\Carbon;

class InviteSupplierAction
{
    public function execute(InviteSupplierData $data): RfqParticipant
    {
        return RfqParticipant::create([
            'rfq_id' => $data->rfqId,
            'supplier_id' => $data->supplierId,

            'status' => RfqParticipantStatus::INVITED,

            'invited_at' => Carbon::now(),
        ]);
    }
}