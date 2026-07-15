<?php

namespace App\Domain\Conversation\Events;

use App\Domain\Conversation\Models\Conversation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Conversation $conversation
    ) {
    }
}