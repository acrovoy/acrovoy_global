<?php

namespace App\Domain\Conversation\Events;

use App\Domain\Conversation\Models\Message;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Message $message
    ) {
    }
}