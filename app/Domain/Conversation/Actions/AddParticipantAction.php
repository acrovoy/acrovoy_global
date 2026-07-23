<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\DTO\AddParticipantData;
use App\Domain\Conversation\Events\ParticipantAdded;
use App\Domain\Conversation\Models\ConversationParticipant;
use Illuminate\Support\Facades\DB;

class AddParticipantAction
{
    /**
     * Добавить участника в Conversation.
     */
    public function execute(AddParticipantData $data): ConversationParticipant
    {
        return DB::transaction(function () use ($data) {

            $participant = ConversationParticipant::firstOrCreate(
                [
                    'conversation_id' => $data->conversationId,

                    'context_type' => $data->contextType,
                    'context_id'   => $data->contextId,

                    'platform_role' => $data->platformRole,
                ],
                [
                    'actor_type' => $data->actorType,
                    'actor_id'   => $data->actorId,
                    'role'       => $data->role,

                    
                ]
            );

            /*
             |--------------------------------------------------------------
             | Событие вызываем только если запись действительно создана
             |--------------------------------------------------------------
             */
            if ($participant->wasRecentlyCreated) {
                event(new ParticipantAdded($participant));
            }

            return $participant;
        });
    }
}