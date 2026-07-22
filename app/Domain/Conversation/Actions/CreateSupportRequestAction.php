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

    string $subject,

    ?string $category,

    string $description

)
{



$conversation = Conversation::create([

'conversation_type' => ConversationType::SUPPORT,

     'title' => $subject,

    'subtitle' => $category,

    'created_by' => auth()->id(),

]);





/*
|--------------------------------------------------------------------------
| Requester
|--------------------------------------------------------------------------
*/


ConversationParticipant::create([

    'conversation_id'=>$conversation->id,

    'context_type'=>$requesterType,

    'context_id'=>$requesterId,

    'role'=>'member',

]);




/*
|--------------------------------------------------------------------------
| Support agent
|--------------------------------------------------------------------------
*/


$admin =
    User::where('role','admin')
        ->first();



ConversationParticipant::create([

    'conversation_id'=>$conversation->id,

    'context_type'=>User::class,

    'context_id'=>$admin->id,

    'role'=>'support',

]);





/*
|--------------------------------------------------------------------------
| First message
|--------------------------------------------------------------------------
*/


$message = $description; 


$message = Message::create([

    'conversation_id'=>$conversation->id,

    'sender_type'=>$requesterType,

    'sender_id'=>$requesterId,

    'message_type'=>'text',

    'message'=>$message,

    'created_by' => auth()->id(),

]);


$conversation->update([

    'last_message_id' => $message->id,

    'last_message_at' => $message->created_at,

]);

return $conversation;


}


}