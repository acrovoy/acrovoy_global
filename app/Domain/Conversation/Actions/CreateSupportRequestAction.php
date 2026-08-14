<?php

namespace App\Domain\Conversation\Actions;


use App\Domain\Conversation\Models\Conversation;
use App\Domain\Conversation\Models\ConversationParticipant;
use App\Domain\Conversation\Models\Message;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;

use App\Domain\Conversation\Enums\ConversationType;



class CreateSupportRequestAction
{


public function execute(

    string $requesterType,
    int $requesterId,
    string $requesterPlatformRole,
    string $subject,
    ?string $category,
    string $description

) {

    

    

    $conversation = Conversation::create([

        'conversation_type' => ConversationType::SUPPORT,

        'title' => $subject,

        'subtitle' => $category,

        'created_by' => auth()->id(),

    ]);

    

    

    ConversationParticipant::create([

        'conversation_id' => $conversation->id,

        'context_type' => $requesterType,

        'context_id' => $requesterId,

        'actor_type' => \App\Models\User::class,
        'actor_id' => auth()->id(),

        'platform_role' => $requesterPlatformRole,

    ]);

    

    $admin = Admin::where('status', 'active')
            ->first();

     if (!$admin) {
            throw new \RuntimeException(
                'No active admin available for support conversation.'
            );
        }

    

    ConversationParticipant::create([

        'conversation_id' => $conversation->id,

        'context_type' => Admin::class,

        'actor_type' => User::class,
        'actor_id' => $admin->user_id,

        'context_id' => $admin->id,

        'platform_role' => 'support',

        'role' => 'support',

    ]);

    

    $message = Message::create([

        'conversation_id' => $conversation->id,

        'sender_type' => $requesterType,

        'sender_id' => $requesterId,

        'message_type' => 'text',

        'message' => $description,

        'created_by' => auth()->id(),

    ]);

    

    

    $conversation->update([

        'last_message_id' => $message->id,

        'last_message_at' => $message->created_at,

    ]);

    

    return $conversation;
}


}