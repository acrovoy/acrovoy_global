<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\DTO\CreateMessageData;
use App\Domain\Conversation\Events\MessageCreated;
use App\Domain\Conversation\Models\Conversation;
use App\Domain\Conversation\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\Conversation\Enums\MessageType;
use App\Domain\Conversation\Enums\ConversationType;

use App\Domain\Conversation\Enums\ConversationStatus;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SendMessageAction
{
    /**
     * Создать новое сообщение.
     */
    public function execute(CreateMessageData $data): Message
    {

    
        return DB::transaction(function () use ($data) {

            $conversation = Conversation::query()
                ->lockForUpdate()
                ->findOrFail($data->conversationId);

            $conversation = Conversation::query()
                ->lockForUpdate()
                ->findOrFail($data->conversationId);

                $messageType = $conversation->conversation_type === ConversationType::NOTICE
    ? MessageType::SYSTEM
    : $data->messageType;


            if (! $conversation->status->canSendMessages()) {

                throw new HttpException(
                    422,
                    'This conversation has been closed.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Создание сообщения
            |--------------------------------------------------------------------------
            */

            $message = Message::create([

                'conversation_id' => $data->conversationId,

                'sender_type' => $data->senderType,
                'sender_id'   => $data->senderId,
                'created_by' => $data->createdBy,

                'message_type' => $messageType,

                'message' => $data->message,

                'payload' => $data->payload,

                'reply_to_message_id' => $data->replyToMessageId,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            |
            | Здесь подключается существующий Media Domain.
            |
            | Реализация зависит от твоего текущего MediaService.
            |
            */

            if (!empty($data->media)) {

                foreach ($data->media as $mediaUuid) {

                    // пример:
                    //
                    // app(MediaService::class)
                    //     ->attachToModel(
                    //          $mediaUuid,
                    //          $message,
                    //          'message_attachments'
                    //     );

                }
            }


            /*
            |--------------------------------------------------------------------------
            | Обновляем последнее сообщение Conversation
            |--------------------------------------------------------------------------
            */

            $conversation->update([
                'last_message_id' => $message->id,
                'last_message_at' => $message->created_at,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Domain Event
            |--------------------------------------------------------------------------
            */

            event(new MessageCreated($message));


            return $message;
        });
    }
}