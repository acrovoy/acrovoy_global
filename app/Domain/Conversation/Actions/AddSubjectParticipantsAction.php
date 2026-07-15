<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Conversation;
use App\Domain\Conversation\DTO\AddParticipantData;
use App\Models\Product;

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


                role:
                    'supplier',

            )

        );

    }
}