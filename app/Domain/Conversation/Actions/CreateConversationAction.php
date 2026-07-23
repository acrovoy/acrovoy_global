<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\DTO\CreateConversationData;
use App\Domain\Conversation\Events\ConversationCreated;
use App\Domain\Conversation\Models\Conversation;
use Illuminate\Support\Facades\DB;

use App\Domain\Conversation\Enums\ConversationStatus;

class CreateConversationAction
{
    /**
     * Создать новый Conversation.
     */
    public function execute(CreateConversationData $data): Conversation
    {
        return DB::transaction(function () use ($data) {

            $conversation = Conversation::create([
                'conversation_type' => $data->conversationType,
                'subject_type'      => $data->subjectType,
                'subject_id'        => $data->subjectId,
                'created_by'        => $data->createdBy,
                'status'            => ConversationStatus::ACTIVE,
            ]);

            event(new ConversationCreated($conversation));

            return $conversation;
        });
    }
}