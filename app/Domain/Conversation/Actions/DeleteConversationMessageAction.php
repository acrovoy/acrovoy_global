<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Message;

class DeleteConversationMessageAction
{
    /**
     * Delete a conversation message.
     */
    public function execute(
        Message $message
    ): void {
        $message->delete();
    }
}