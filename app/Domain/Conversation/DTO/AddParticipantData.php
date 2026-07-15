<?php

namespace App\Domain\Conversation\DTO;

final class AddParticipantData
{
    public function __construct(

        public int $conversationId,

        /**
         * Кто физически участвует.
         *
         * Обычно User.
         */
        public string $actorType,

        public int $actorId,

        /**
         * От имени какого контекста ведется переписка.
         *
         * Supplier
         * Buyer
         * PrivateUser
         * LogisticsCompany
         */
        public string $contextType,

        public int $contextId,

        /**
         * owner
         * member
         * support
         */
        public ?string $role = null,

    ) {}
}