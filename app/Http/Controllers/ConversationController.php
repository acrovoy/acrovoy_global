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
use App\Domain\Conversation\Resolvers\NoticeConversationHeaderResolver;
use App\Domain\Conversation\Resolvers\RfqHeaderResolver;
use App\Domain\Conversation\Resolvers\ProjectHeaderResolver;
use App\Domain\Conversation\Resolvers\UserHeaderResolver;
use App\Services\Company\ActiveContextService;
use App\Models\User;
use App\Domain\Conversation\Enums\ConversationType;


use App\Domain\Conversation\Actions\FormatConversationMessageAction;

class ConversationController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversationService,
        private ConversationHeaderService $headerService,
        private ActiveContextService $context,
        private FormatConversationMessageAction $formatMessage,
    ) {
        

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


        
        $identity = $this->context->identity();
        $conversationType = null;

if (
    $data['subject_type'] === User::class &&
    $identity['platform_role'] === 'admin'
) {
    $conversationType = ConversationType::PRIVATE;
}

        /*
        |--------------------------------------------------------------------------
        | Создаем или находим Conversation
        |--------------------------------------------------------------------------
        */

        $conversation = $this->conversationService
            ->findOrCreateBusinessConversation(
                subjectType: $data['subject_type'],
                subjectId: $data['subject_id'],
                createdBy: $identity['user_id'],
                contextType: $identity['entity_type'],
                contextId: $identity['entity_id'],
                platformRole: $identity['platform_role'],
                conversationType: $conversationType,
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
                        User::class,

                    actorId:
                        $identity['user_id'],


                    contextType:
                        $identity['entity_type'],

                    contextId:
                        $identity['entity_id'],


                    platformRole:
                        $identity['platform_role'],
                )

            );


            if (
                    $data['subject_type'] === User::class &&
                    $identity['platform_role'] === 'admin'
                ) {
                    $user = User::findOrFail($data['subject_id']);

                    $this->conversationService->addParticipant(
                        new AddParticipantData(
                            conversationId: $conversation->id,

                            actorType: User::class,
                            actorId: $user->id,

                            contextType: User::class,
                            contextId: $user->id,

                            platformRole: $user->setting('platform_mode', 'buyer'),
                        )
                    );
                }


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

                'status' => $conversation->status->value,

            ],


            'header' => $this->headerService->resolve($conversation),


            'messages' => $conversation->messages
    ->sortBy('created_at')
    ->values()
    ->map(fn ($message) => $this->formatMessage->execute(
        $message,
        auth()->user()->timezone ?? config('app.timezone'),
        $identity['entity_type'],
        $identity['entity_id'],
    )),

        ]);
    }



    /**
     * Формирование шапки.
     *
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

    'message' => $this->formatMessage->execute(
    $message,
    auth()->user()->timezone ?? config('app.timezone'),
    $this->context->type(),
    $this->context->id(),
),

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
    ->map(fn ($message) => $this->formatMessage->execute(
        $message,
        auth()->user()->timezone ?? config('app.timezone'),
        $this->context->type(),
        $this->context->id(),
    )),
    ]);
}





}