<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Enums\ConversationType;
use App\Domain\Conversation\Enums\MessageType;
use App\Domain\Conversation\Models\Conversation;
use App\Domain\Conversation\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use App\Domain\Conversation\Models\ConversationParticipant;


class CreateNoticeConversationAction
{
    public function execute(
        string $title,
        string $subtitle,
        string $description,
        int $createdBy,
    ): Conversation {

        return DB::transaction(function () use (
            $title,
            $subtitle,
            $description,
            $createdBy
        ) {

            /*
            |--------------------------------------------------------------------------
            | Conversation
            |--------------------------------------------------------------------------
            */

            $conversation = Conversation::create([

                'conversation_type' => ConversationType::NOTICE,

                'title' => $title,

                'subtitle' => $subtitle,

                'created_by' => $createdBy,

            ]);


            /*
            |--------------------------------------------------------------------------
            | First message
            |--------------------------------------------------------------------------
            */

            $message = Message::create([

                'conversation_id' => $conversation->id,

                'sender_type' => User::class,

                'sender_id' => $createdBy,

                'created_by' => $createdBy,

                'message_type' => MessageType::SYSTEM,

                'message' => $description,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Conversation info
            |--------------------------------------------------------------------------
            */

            $conversation->update([

                'last_message_id' => $message->id,

                'last_message_at' => $message->created_at,

            ]);

            ConversationParticipant::create([

                'conversation_id' => $conversation->id,

                'actor_type'   => User::class,
                'actor_id'     => auth()->id(),

                'context_type' => User::class,
                'context_id'   => auth()->id(),

                'platform_role' => 'admin',

                'role' => 'admin',



            ]);


            return $conversation->fresh([
                'messages',
            ]);
        });
    }
}
