<?php

namespace App\Domain\Conversation\Queries;

use App\Domain\Conversation\Enums\ConversationType;
use App\Domain\Conversation\Models\Conversation;

class NoticeConversationsQuery
{
    public function execute(?string $search = null)
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

            ->where(
                'conversation_type',
                ConversationType::NOTICE
            )

             ->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%");

            });

        })

            ->latest('updated_at');
    }
}