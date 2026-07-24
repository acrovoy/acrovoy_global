<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Conversation;

class ReopenConversationAction
{
    /**
     * Повторно открыть Conversation.
     */
    public function execute(
        Conversation $conversation
    ): Conversation {

        if ($conversation->status === 'active') {
            return $conversation;
        }

        $conversation->update([
            'status' => 'active',
            'closed_at' => null,
            'closed_by' => null,
        ]);

        return $conversation->refresh();
    }
}