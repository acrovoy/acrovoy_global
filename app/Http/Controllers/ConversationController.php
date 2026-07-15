<?php

namespace App\Http\Controllers;

use App\Domain\Conversation\DTO\AddParticipantData;
use App\Domain\Conversation\Services\ConversationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Domain\Conversation\Models\Conversation;

use App\Domain\Conversation\DTO\CreateMessageData;
use App\Domain\Conversation\Enums\MessageType;

use App\Domain\Conversation\Services\ConversationHeaderService;
use App\Domain\Conversation\Resolvers\ProductHeaderResolver;
use App\Domain\Conversation\Resolvers\RfqHeaderResolver;
use App\Domain\Conversation\Resolvers\ProjectHeaderResolver;
use App\Services\Company\ActiveContextService;

class ConversationController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversationService,
        private ConversationHeaderService $headerService,
        private ActiveContextService $context,
    ) {

    $this->headerService = new ConversationHeaderService();

    $this->headerService
        ->addResolver(new ProductHeaderResolver())
        ->addResolver(new RfqHeaderResolver())
        ->addResolver(new ProjectHeaderResolver());

    }


    /**
     * Открыть или создать Conversation.
     */
    public function open(Request $request)
    {
        $data = $request->validate([

            'subject_type' => [
                'required',
                'string',
            ],

            'subject_id' => [
                'required',
                'integer',
            ],

        ]);


        $user = Auth::user();



        /*
        |--------------------------------------------------------------------------
        | Создаем или находим Conversation
        |--------------------------------------------------------------------------
        */

        $conversation = $this->conversationService
            ->findOrCreateBusinessConversation(
                subjectType: $data['subject_type'],
                subjectId: $data['subject_id'],
                createdBy: $user->id,
                contextType: $this->context->type(),
                contextId: $this->context->id(),
            );


            $this->conversationService
             ->syncSubjectParticipants($conversation);

        /*
        |--------------------------------------------------------------------------
        | Добавляем текущего пользователя
        |--------------------------------------------------------------------------
        |
        | Сейчас берем личный context.
        |
        | Потом здесь будет ActiveContextService:
        |
        | Supplier Company
        | Buyer Company
        | Personal User
        |
        */

        $this->conversationService
            ->addParticipant(

                new AddParticipantData(

                    conversationId:
                        $conversation->id,

                    actorType:
                        get_class($user),

                    actorId:
                        $user->id,


                    contextType:
                        $this->context->type(),

                    contextId:
                        $this->context->id(),


                    role:
                        'member',
                )

            );



        /*
        |--------------------------------------------------------------------------
        | Загружаем данные для Drawer
        |--------------------------------------------------------------------------
        */

        $conversation->load([
            'participants',
            'messages.sender',
            
        ]);



        return response()->json([

            'conversation' => [
                'id' =>
                    $conversation->id,

                'type' =>
                    $conversation->conversation_type,

            ],


            'header' => $this->headerService->resolve($conversation),


            'messages' => $conversation->messages
    ->sortBy('created_at')
    ->values()
    ->map(fn ($message) => $this->transformMessage($message)),

        ]);
    }



    /**
     * Формирование шапки.
     *
     * Пока заглушка.
     *
     * Позже здесь будет Subject Resolver:
     *
     * ProductResolver
     * RfqResolver
     * ProjectResolver
     */
    
    public function message(Request $request)
{
    $data = $request->validate([

        'conversation_id'=>'required|integer',

        'message'=>'required|string',

    ]);


    $message =
        $this->conversationService
        ->sendMessage(

            new CreateMessageData(

                conversationId:
                    $data['conversation_id'],
                senderType: $this->context->type(),
                senderId: $this->context->id(),
                messageType: MessageType::TEXT,
                createdBy: auth()->id(),
                message: $data['message'],
            )
        );

        $message->load('sender');

    return response()->json([

    'message' =>

        $this->transformMessage($message)

]);
}

public function messages(Conversation $conversation)
{
    $conversation->load([
        'messages.sender',
        
    ]);

    return response()->json([
        'messages' => $conversation->messages
            ->sortBy('created_at')
            ->values()
            ->map(fn ($message) => $this->transformMessage($message)),
    ]);
}


private function transformMessage($message): array
{

$isMine =
    $message->sender_type === $this->context->type()
    && (int)$message->sender_id === (int)$this->context->id();

    

    return [

    'id' => $message->id,

    'message' => $message->message,

    'type' => $message->message_type,

    'created_at' => optional($message->created_at)->format('H:i'),

    'is_mine' => $isMine,

    'sender' => [

        'id' => $message->sender?->id,

        'name' => $message->creator?->role === 'admin'
    ? 'ACROVOY'
    : match ($message->sender_type) {

        \App\Models\User::class => trim(
            ($message->sender?->name ?? '') . ' ' .
            ($message->sender?->last_name ?? '')
        ),

        default => $message->sender?->name,
    },

        'avatar' =>
            $message->creator?->avatar()?->cdn_url
            ?? asset('images/default-avatar.png'),

        'position' => $message->sender?->position,

        'company' => $message->sender?->company?->name,

    ],

    'attachments' => [],

];
   
}


}