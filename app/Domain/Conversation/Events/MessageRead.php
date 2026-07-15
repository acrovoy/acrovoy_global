<?php

namespace App\Domain\Conversation\Events;

use App\Domain\Conversation\Models\ConversationParticipant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public ConversationParticipant $participant
    ) {
    }
}