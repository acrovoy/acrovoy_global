<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Events\MessageRead;
use App\Domain\Conversation\Models\ConversationParticipant;
use Illuminate\Support\Facades\DB;

class MarkConversationReadAction
{
    /**
     * Отметить Conversation как прочитанный.
     */
    public function execute(
    int $conversationId,
    string $contextType,
    int $contextId
): ?ConversationParticipant {

    return DB::transaction(function () use (
        $conversationId,
        $contextType,
        $contextId
    ) {

        $participant = ConversationParticipant::query()
            ->where('conversation_id', $conversationId)
            ->where('context_type', $contextType)
            ->where('context_id', $contextId)
            ->lockForUpdate()
            ->first();

        if (!$participant) {
            return null;
        }

        $participant->update([
            'last_read_at' => now(),
        ]);

        event(new MessageRead($participant));

        return $participant;
    });
}


}