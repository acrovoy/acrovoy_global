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
use App\Domain\Conversation\Actions\DeleteEmptyConversationsAction;
use App\Domain\Conversation\Actions\DeleteConversationMessageAction;
use App\Domain\Conversation\Actions\CloseConversationAction;
use App\Domain\Conversation\Actions\ReopenConversationAction;
use App\Domain\Conversation\Actions\DeleteConversationAction;
use App\Domain\Conversation\Actions\CreateNoticeConversationAction;



class ConversationService
{
    public function __construct(

        private readonly FindConversationAction $findConversation,
        private readonly FindOrCreateConversationAction $findOrCreateConversation,
        private readonly AddParticipantAction $addParticipant,
        private readonly SendMessageAction $sendMessage,
        private readonly MarkConversationReadAction $markRead,
        private AddSubjectParticipantsAction $addSubjectParticipants,
        private readonly DeleteEmptyConversationsAction $deleteEmptyConversations,
        private readonly DeleteConversationMessageAction $deleteConversationMessageAction,
        private readonly CloseConversationAction $closeConversationAction,
        private readonly ReopenConversationAction $reopenConversationAction,
        private readonly DeleteConversationAction $deleteConversationAction,
        private readonly CreateNoticeConversationAction $createNoticeConversationAction,

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
        ?ConversationType $conversationType = null,
    ): Conversation {

        $conversationType ??= ConversationType::BUSINESS;
        
        $data = new CreateConversationData(
            conversationType: $conversationType,
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

    /**
 * Удалить пустые Conversation.
 */
public function deleteEmptyConversations(): int
{
    return $this->deleteEmptyConversations
        ->execute();
}

public function getConversationStatistics(): array
{
    return [
        'total' => Conversation::has('messages')->count(),

        'empty' => Conversation::doesntHave('messages')->count(),
    ];
}

public function deleteMessage(
    Message $message
): void
{
    $this->deleteConversationMessageAction
        ->execute($message);
}

public function closeConversation(
    Conversation $conversation
): Conversation
{
    return $this->closeConversationAction
        ->execute($conversation);
}

public function reopenConversation(
    Conversation $conversation
): Conversation
{
    return $this->reopenConversationAction
        ->execute($conversation);
}

public function deleteConversation(
    Conversation $conversation
): void
{
    $this->deleteConversationAction
        ->execute($conversation);
}

public function createNotice(
    string $title,
    string $subtitle,
    string $description,
    int $createdBy,
): Conversation {

    return $this->createNoticeConversationAction
        ->execute(
            $title,
            $subtitle,
            $description,
            $createdBy,
        );
}
    
}