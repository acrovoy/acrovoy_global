<?php

namespace App\Domain\Conversation\Queries;

use App\Domain\Conversation\Models\Conversation;
use App\Models\Supplier;

class SupplierConversationsQuery
{

    public function execute(
        string $supplierType,
        int $supplierId
    ) {

        return Conversation::query()

            ->whereHas(
                'participants',
                function ($query) use ($supplierType, $supplierId) {


                    $query->where(
                        'context_type',
                        $supplierType
                    )


                    ->where(
                        'context_id',
                        $supplierId
                    );


                }
            )


            ->with([


                'participant' => function ($query) use ($supplierType, $supplierId) {

                    $query
                        ->where('context_type', $supplierType)
                        ->where('context_id', $supplierId);

                },


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