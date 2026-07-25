<?php

namespace App\Domain\Conversation\Queries;

use App\Domain\Conversation\Models\Conversation;

class AllConversationsQuery
{
    public function execute(
        ?string $search = null,
    )
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