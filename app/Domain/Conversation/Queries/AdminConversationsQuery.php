<?php

namespace App\Domain\Conversation\Queries;

use App\Domain\Conversation\Models\Conversation;

class AdminConversationsQuery
{
    public function execute()
    {

      
        return Conversation::query()

            ->with([

                'participants',

                'lastMessage',

                'messages' => function ($query) {

                    $query
                        ->latest()
                        ->limit(1);

                },

            ])

            ->latest('updated_at');
    }
}