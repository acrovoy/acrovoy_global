<?php

namespace App\Domain\Conversation\Enums;

enum ConversationStatus: string
{
    /**
     * Conversation is active.
     *
     * Participants can exchange messages.
     */
    case ACTIVE = 'active';

    /**
     * Conversation has been closed.
     *
     * New messages cannot be sent.
     */
    case CLOSED = 'closed';

    /**
     * Conversation is archived.
     *
     * Read-only state for historical conversations.
     */
    case ARCHIVED = 'archived';

    /**
     * Whether new messages are allowed.
     */
    public function canSendMessages(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Whether the conversation is read-only.
     */
    public function isReadOnly(): bool
    {
        return $this !== self::ACTIVE;
    }
}