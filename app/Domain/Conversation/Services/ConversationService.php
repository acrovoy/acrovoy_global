<?php

namespace App\Domain\Conversation\Services;

use App\Domain\Conversation\Actions\AddParticipantAction;
use App\Domain\Conversation\Actions\FindConversationAction;
use App\Domain\Conversation\Actions\FindOrCreateConversationAction;
use App\Domain\Conversation\Actions\MarkConversationReadAction;
use App\Domain\Conversation\Actions\SendMessageAction;
use App\Domain\Conversation\DTO\AddParticipantData;
use App\Domain\Conversation\DTO\CreateConversationData;
use App\Domain\Conversation\DTO\CreateMessageData;
use App\Domain\Conversation\Enums\ConversationType;
use App\Domain\Conversation\Enums\MessageType;
use App\Domain\Conversation\Models\Conversation;
use App\Domain\Conversation\Models\ConversationParticipant;
use App\Domain\Conversation\Models\Message;
use App\Domain\Conversation\Actions\AddSubjectParticipantsAction;

class ConversationService
{
    public function __construct(

        private readonly FindConversationAction $findConversation,

        private readonly FindOrCreateConversationAction $findOrCreateConversation,

        private readonly AddParticipantAction $addParticipant,

        private readonly SendMessageAction $sendMessage,

        private readonly MarkConversationReadAction $markRead,
        private AddSubjectParticipantsAction $addSubjectParticipants,

    ) {
    }


    /**
     * Найти Conversation.
     */
    public function find(
        CreateConversationData $data
    ): ?Conversation {

        return $this->findConversation->execute($data);
    }


    /**
     * Найти существующий бизнес-диалог
     * или создать новый.
     */
    public function findOrCreateBusinessConversation(
        string $subjectType,
        int $subjectId,
        int $createdBy,
        ?string $contextType = null,
        ?int $contextId = null,
        ?string $platformRole = null,
    ): Conversation {

        $data = new CreateConversationData(
            conversationType: ConversationType::BUSINESS,
            subjectType: $subjectType,
            subjectId: $subjectId,
            platformRole: $platformRole,
            createdBy: $createdBy,
            contextType: $contextType,
            contextId: $contextId,
        );


        return $this->findOrCreateConversation
            ->execute($data);
    }

   

    public function syncSubjectParticipants(
    Conversation $conversation
): void
{
    $this->addSubjectParticipants
         ->execute($conversation);
}

    /**
     * Создать личный Conversation.
     */
    public function findOrCreatePrivateConversation(
        int $createdBy,
        ?string $platformRole = null,
    ): Conversation {

        $data = new CreateConversationData(
            conversationType: ConversationType::PRIVATE,
            subjectType: null,
            subjectId: null,
            platformRole: $platformRole,
            createdBy: $createdBy,
        );


        return $this->findOrCreateConversation
            ->execute($data);
    }


    /**
     * Добавить участника.
     */
    public function addParticipant(
        AddParticipantData $data
    ): ConversationParticipant {

        return $this->addParticipant
            ->execute($data);
    }


    /**
     * Отправить сообщение.
     */
    public function sendMessage(
        CreateMessageData $data
    ): Message {

        return $this->sendMessage
            ->execute($data);
    }


    /**
     * Быстрая отправка обычного текста.
     *
     * Удобно для контроллеров и сервисов.
     */
    public function sendTextMessage(
        Conversation $conversation,
        string $senderType,
        int $senderId,
        string $message,
    ): Message {

        $data = new CreateMessageData(

            conversationId: $conversation->id,

            senderType: $senderType,

            senderId: $senderId,

            messageType: MessageType::TEXT,

            message: $message,

        );


        return $this->sendMessage
            ->execute($data);
    }


    /**
     * Отметить Conversation прочитанным.
     */
    public function markAsRead(
        int $conversationId,
        string $contextType,
        int $contextId,
    ): ConversationParticipant {

        return $this->markRead
            ->execute(
                $conversationId,
                $contextType,
                $contextId
            );
    }
}