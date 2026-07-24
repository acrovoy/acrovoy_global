<?php

namespace App\Domain\Conversation\Queries;

use App\Domain\Conversation\Models\Conversation;

class SupportConversationsQuery
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

            ->whereHas('participants', function ($query) {

                $query->where('role', 'support');

            })

            ->latest('updated_at');
    }
}