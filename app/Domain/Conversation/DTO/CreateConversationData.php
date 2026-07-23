<?php

namespace App\Domain\Conversation\DTO;

use App\Domain\Conversation\Enums\ConversationType;

final class CreateConversationData
{
    public function __construct(

        public readonly ConversationType $conversationType,

        public readonly ?string $subjectType,

        public readonly ?int $subjectId,

        /**
         * Роль участника в данном контексте.
         *
         * User context:
         * buyer / supplier
       
         */
        public readonly ?string $platformRole,

        /**
         * Кто создал Conversation.
         *
         * Это пользователь, который инициировал действие.
         */
        public readonly int $createdBy,


        /*
        |--------------------------------------------------------------------------
        | Context инициатора
        |--------------------------------------------------------------------------
        |
        | Например:
        |
        | Buyer Company
        | Supplier Company
        | Personal User
        |
        | Используется для поиска правильного Conversation.
        |
        */
        public readonly ?string $contextType = null,

        public readonly ?int $contextId = null,

    ) {
    }
}