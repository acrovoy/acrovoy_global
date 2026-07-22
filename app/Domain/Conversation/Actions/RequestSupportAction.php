<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Conversation;
use App\Domain\Conversation\Models\ConversationParticipant;
use App\Domain\Conversation\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RequestSupportAction
{
    public function execute(
        Conversation $conversation,
        string $requesterType,
        int $requesterId,
        ?string $reason = null
    ): Message {

        return DB::transaction(function () use (
            $conversation,
            $requesterType,
            $requesterId,
            $reason,
        ) {

            /*
            |--------------------------------------------------------------------------
            | Find Support Admin
            |--------------------------------------------------------------------------
            */

            $admin = User::query()
                ->where('role', 'admin')
                ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Add Support Participant
            |--------------------------------------------------------------------------
            */

            ConversationParticipant::firstOrCreate(

                [
                    'conversation_id' => $conversation->id,
                    'context_type'    => User::class,
                    'context_id'      => $admin->id,
                ],

                [
                    'role' => 'support',
                ]

            );

            /*
            |--------------------------------------------------------------------------
            | Build system message
            |--------------------------------------------------------------------------
            */

            $text = class_basename($requesterType) . ' requested support.';

            if ($reason) {
                $text .= "\nReason: {$reason}";
            }

            /*
            |--------------------------------------------------------------------------
            | Create System Message
            |--------------------------------------------------------------------------
            */

            $message = Message::create([

                'conversation_id' => $conversation->id,

                'sender_type' => $requesterType,
                'sender_id'   => $requesterId,

                'created_by'  => auth()->id(),

                'message_type' => 'system',

                'message' => $text,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Update conversation
            |--------------------------------------------------------------------------
            */

            $conversation->update([

                'last_message_id' => $message->id,

                'last_message_at' => $message->created_at,

            ]);

            return $message;

        });

    }
}