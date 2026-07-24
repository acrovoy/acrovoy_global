<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Conversation;

class CloseConversationAction
{
    public function execute(
        Conversation $conversation
    ): Conversation {

        $conversation->update([
            'status' => 'closed',
        ]);
        

        return $conversation->fresh();
    }
}