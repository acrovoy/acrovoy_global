<?php

namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\DTO\CreateRfqData;
use App\Domain\RFQ\Enums\RfqStatus;
use App\Domain\RFQ\Events\RfqCreated;
use App\Domain\RFQ\Models\Rfq;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Domain\RFQ\Enums\RfqParticipantStatus;
use App\Domain\RFQ\Models\RfqParticipant;

class CreateCustomizationRfqAction
{
    public function execute(
        CreateRfqData $data,
        $buyerId,
        $buyerType,
        int $createdBy,
        string $supplierType,
        int $supplierId,
        int $productId,
        ?int $projectId = null,

    ): Rfq {



        $customization = is_null($projectId);

        $closedAt = now()->addDays(30);

        $rfq = Rfq::create([
            'buyer_type'    => $buyerType,
            'buyer_id'      => $buyerId,
            'created_by'    => $createdBy,

            'project_id'    => $projectId,
            'title'         => $data->title,
            'description'   => $data->description,
            'category_id'   => 84, 
            'product_id'    => $productId,
            'customization' => $customization,
            'type'          => $data->type,
            'status'        => RfqStatus::DRAFT,
            'closed_at'     => $closedAt,
        ]);

        RfqParticipant::updateOrCreate(
            [
                'rfq_id' => $rfq->id,
                'participant_type' => $supplierType,
                'participant_id' => $supplierId,
            ],
            [
                'status' => RfqParticipantStatus::INVITED,
                'invited_at' => Carbon::now(),
            ]
        );

        return $rfq;
    }
}