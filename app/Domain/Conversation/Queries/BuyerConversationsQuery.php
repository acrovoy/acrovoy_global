<?php

namespace App\Domain\Conversation\Queries;

use App\Domain\Conversation\Models\Conversation;
use App\Models\Supplier;

class BuyerConversationsQuery
{

    public function execute(
        string $contextType,
        int $contextId,
        ?string $platformRole = null,
        ?string $search = null,
    ) {

        return Conversation::query()

            ->whereHas(
                'participants',
                function ($query) use ($contextType, $contextId, $platformRole) {


                    $query
                        ->where('context_type', $contextType)
                        ->where('context_id', $contextId);

                    if ($platformRole) {
                        $query->where('platform_role', $platformRole);
                    }


                }
            )


            ->with([


            'participant' => function ($query) use ($contextType, $contextId, $platformRole) {

                    $query
                        ->where('context_type', $contextType)
                        ->where('context_id', $contextId);

                    if ($platformRole) {
                        $query->where('platform_role', $platformRole);
                    }

                },

                'participants',
                'lastMessage',

                'messages' => function($query){

                    $query
                        ->latest()
                        ->limit(1);

                },

            ])


            ->when($search, function ($query) use ($search) {

            $query->where(function ($query) use ($search) {

                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%");

            });

        })

                    ->latest('updated_at');


    }

}