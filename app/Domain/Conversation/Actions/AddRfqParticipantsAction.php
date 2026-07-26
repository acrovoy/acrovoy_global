<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Conversation;
use App\Domain\Conversation\DTO\AddParticipantData;
use App\Models\Product;
use App\Models\User;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Project\Models\Project;

use Illuminate\Support\Facades\Log;

class AddRfqParticipantsAction
{
    public function __construct(
        private AddParticipantAction $addParticipant
    ) {}


    public function execute(
        Conversation $conversation,
        array $identity
    ): void {


        match ($identity['platform_role']) {


            'buyer' =>
            $this->addRfqSupplier(
                $conversation
            ),

            'supplier' =>
            $this->addRfqBuyer(
                $conversation
            ),




            default =>
            null,
        };
    }



    private function addRfqSupplier(
        Conversation $conversation
    ): void {


        $offer =
            RfqOffer::find(
                $conversation->subject_id
            );


        if (!$offer) {
            return;
        }



        $supplier =
            $offer->participant;



        if (!$supplier) {
            return;
        }



        $this->addParticipant->execute(

            new AddParticipantData(

                conversationId: $conversation->id,


                actorType: get_class($supplier),


                actorId: $supplier->id,


                contextType: get_class($supplier),


                contextId: $supplier->id,


                platformRole: 'supplier',

            )

        );
    }

    private function addRfqBuyer(
    Conversation $conversation
): void {

    $offer = RfqOffer::find(
        $conversation->subject_id
    );

    if (!$offer) {

        Log::warning('RFQ Buyer Participant: Offer not found', [
            'conversation_id' => $conversation->id,
            'subject_id'      => $conversation->subject_id,
        ]);

        return;
    }

    $rfq = $offer->rfq;

    if (!$rfq) {

        Log::warning('RFQ Buyer Participant: RFQ not found', [
            'conversation_id' => $conversation->id,
            'offer_id'        => $offer->id,
        ]);

        return;
    }

    $buyer = $rfq->buyer;

    if (!$buyer) {

        Log::warning('RFQ Buyer Participant: Buyer not found', [
            'conversation_id' => $conversation->id,
            'rfq_id'          => $rfq->id,
        ]);

        return;
    }

    

    $this->addParticipant->execute(

        new AddParticipantData(

            conversationId: $conversation->id,

            actorType: User::class,

            actorId: $rfq->created_by,

            contextType: $rfq->buyer_type,

            contextId: $rfq->buyer_id,

            platformRole: 'buyer',

        )

    );
}
}
