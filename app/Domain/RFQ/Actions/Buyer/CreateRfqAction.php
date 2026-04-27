<?php

namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\DTO\CreateRfqData;
use App\Domain\RFQ\Enums\RfqStatus;
use App\Domain\RFQ\Events\RfqCreated;
use App\Domain\RFQ\Models\Rfq;
use Illuminate\Support\Str;

class CreateRfqAction
{
    public function execute(CreateRfqData $data, $buyer, $buyerType, int $createdBy): Rfq
{
    return Rfq::create([
        'buyer_type' => $buyerType,
        'buyer_id'   => $buyer->id,

        'created_by' => $createdBy,
        'title' => $data->title,
        'description' => $data->description,
        'type' => $data->type,
        'status' => RfqStatus::DRAFT,
    ]);
}
}