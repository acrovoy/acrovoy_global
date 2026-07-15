<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\DTO\CreateConversationData;
use App\Domain\Conversation\Models\Conversation;

class FindOrCreateConversationAction
{
    public function __construct(
        private readonly FindConversationAction $findConversation,
        private readonly CreateConversationAction $createConversation,
    ) {
    }

    /**
     * Найти существующий Conversation
     * или создать новый.
     */
    public function execute(CreateConversationData $data): Conversation
    {
        $conversation = $this->findConversation->execute($data);

        if ($conversation) {
            return $conversation;
        }

        return $this->createConversation->execute($data);
    }
}