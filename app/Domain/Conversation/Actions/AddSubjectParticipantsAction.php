<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Conversation;
use App\Domain\Conversation\DTO\AddParticipantData;
use App\Models\Product;
use App\Domain\RFQ\Models\Rfq;
use App\Models\Buyer;
use App\Models\Supplier;


class AddSubjectParticipantsAction
{
    public function __construct(
        private AddParticipantAction $addParticipant
    ) {
    }


    public function execute(
        Conversation $conversation
    ): void {


        match ($conversation->subject_type) {


            Product::class =>
                $this->addProductSupplier(
                    $conversation
                ),

             

                Buyer::class =>
                $this->addBuyer($conversation),

            Supplier::class =>
                $this->addSupplier($conversation),

            


            default =>
                null,

        };

    }



    private function addProductSupplier(
        Conversation $conversation
    ): void {


        $product =
            Product::find(
                $conversation->subject_id
            );


        if (!$product) {
            return;
        }



        $supplier =
            $product->supplier;



        if (!$supplier) {
            return;
        }



        $this->addParticipant->execute(

            new AddParticipantData(

                conversationId:
                    $conversation->id,


                actorType:
                    get_class($supplier),


                actorId:
                    $supplier->id,


                contextType:
                    get_class($supplier),


                contextId:
                    $supplier->id,


                platformRole:
                    'supplier',

            )

        );

    }


      private function addBuyer(
        Conversation $conversation
    ): void {

        $buyer = Buyer::find(
            $conversation->subject_id
        );

        if (!$buyer) {
            return;
        }

        $this->addParticipant->execute(

            new AddParticipantData(

                conversationId:
                    $conversation->id,

                actorType:
                    Buyer::class,

                actorId:
                    $buyer->id,

                contextType:
                    Buyer::class,

                contextId:
                    $buyer->id,

                platformRole:
                    'buyer',
            )
        );
    }


    private function addSupplier(
        Conversation $conversation
    ): void {

        $supplier = Supplier::find(
            $conversation->subject_id
        );

        if (!$supplier) {
            return;
        }

        $this->addParticipant->execute(

            new AddParticipantData(

                conversationId:
                    $conversation->id,

                actorType:
                    Supplier::class,

                actorId:
                    $supplier->id,

                contextType:
                    Supplier::class,

                contextId:
                    $supplier->id,

                platformRole:
                    'supplier',
            )
        );
    }
    
    
    
}