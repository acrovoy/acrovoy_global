<?php

namespace App\Domain\Conversation\Actions;


use App\Domain\Conversation\Models\Conversation;
use App\Domain\Conversation\Models\ConversationParticipant;
use App\Domain\Conversation\Models\Message;
use App\Models\User;
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

    Log::info('----- CreateSupportRequestAction -----');

    Log::info('Arguments', [
        'requesterType' => $requesterType,
        'requesterId' => $requesterId,
        'requesterPlatformRole' => $requesterPlatformRole,
        'subject' => $subject,
        'category' => $category,
        'description' => $description,
    ]);

    Log::info('Creating conversation');

    $conversation = Conversation::create([

        'conversation_type' => ConversationType::SUPPORT,

        'title' => $subject,

        'subtitle' => $category,

        'created_by' => auth()->id(),

    ]);

    Log::info('Conversation created', [
        'id' => $conversation->id,
    ]);

    Log::info('Creating requester participant');

    ConversationParticipant::create([

        'conversation_id' => $conversation->id,

        'context_type' => $requesterType,

        'context_id' => $requesterId,

        'platform_role' => $requesterPlatformRole,

    ]);

    Log::info('Requester participant created');

    Log::info('Searching admin');

    $admin = User::where('role', 'admin')->first();

    Log::info('Admin found', [
        'id' => $admin?->id,
    ]);

    Log::info('Creating support participant');

    ConversationParticipant::create([

        'conversation_id' => $conversation->id,

        'context_type' => User::class,

        'context_id' => $admin->id,

        'platform_role' => 'support',

        'role' => 'support',

    ]);

    Log::info('Support participant created');

    Log::info('Creating first message');

    $message = Message::create([

        'conversation_id' => $conversation->id,

        'sender_type' => $requesterType,

        'sender_id' => $requesterId,

        'message_type' => 'text',

        'message' => $description,

        'created_by' => auth()->id(),

    ]);

    Log::info('Message created', [
        'id' => $message->id,
    ]);

    Log::info('Updating conversation');

    $conversation->update([

        'last_message_id' => $message->id,

        'last_message_at' => $message->created_at,

    ]);

    Log::info('Conversation updated');

    Log::info('----- DONE -----');

    return $conversation;
}


}