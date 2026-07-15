<?php

namespace App\Domain\Conversation\Queries;

use App\Domain\Conversation\Models\Conversation;
use App\Models\Supplier;

class BuyerConversationsQuery
{

    public function execute(
        string $buyerType,
        int $buyerId
    ) {

        return Conversation::query()

            ->whereHas(
                'participants',
                function ($query) use ($buyerType, $buyerId) {


                    $query->where(
                        'context_type',
                        $buyerType
                    )


                    ->where(
                        'context_id',
                        $buyerId
                    );


                }
            )


            ->with([

                'participants',
                'lastMessage',

                'messages' => function($query){

                    $query
                        ->latest()
                        ->limit(1);

                },

            ])


            ->latest('updated_at');


    }

}