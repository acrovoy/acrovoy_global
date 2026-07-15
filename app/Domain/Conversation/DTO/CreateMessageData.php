<?php

namespace App\Domain\Conversation\DTO;

use App\Domain\Conversation\Enums\MessageType;

final class CreateMessageData
{
    public function __construct(

        public readonly int $conversationId,

        public readonly string $senderType,

        public readonly int $senderId,

        public readonly MessageType $messageType,
        
        public readonly ?int $createdBy = null,

        public readonly ?string $message = null,

        public readonly ?array $payload = null,

        public readonly ?int $replyToMessageId = null,

        /**
         * UUID уже загруженных файлов Media.
         *
         * Например:
         * [
         *     '7b8a...',
         *     'c124...',
         * ]
         */
        public readonly array $media = [],

    ) {
    }
}