<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Conversation\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Log;

use App\Domain\Conversation\Queries\SupportConversationsQuery;
use App\Domain\Conversation\Queries\AllConversationsQuery;
use App\Domain\Conversation\Queries\NoticeConversationsQuery;
use App\Services\Company\ActiveContextService;

use App\Domain\Conversation\Actions\MarkConversationReadAction;
use App\Domain\Conversation\Actions\LoadNewMessagesAction;
use App\Domain\Conversation\Actions\CreateNoticeConversationAction;

use App\Services\Date\UserDateFormatter;

use App\Domain\Conversation\Services\ConversationService;
use App\Domain\Conversation\Services\ConversationHeaderService;
use App\Domain\Conversation\Models\Message;

class AdminMessengerController extends Controller
{

    public function __construct(
        private ActiveContextService $context,
        private ConversationHeaderService $headerService,
        private MarkConversationReadAction $markConversationRead,
        private LoadNewMessagesAction $loadNewMessages,
        private UserDateFormatter $dateFormatter,
        private SupportConversationsQuery $supportConversations,
        private AllConversationsQuery $allConversations,
        private ConversationService $conversationService,
        private NoticeConversationsQuery $noticeConversations,
        
    ) {}
    /**
     * Messenger page.
     */
    public function index()
    {
        return view(
            'dashboard.admin.messenger.index'
        );
    }

public function allMessages()
{
    return view(
        'dashboard.admin.messenger.all-messages'
    );
}

public function noticeMessages()
{
    return view(
        'dashboard.admin.messenger.notice-messages'
    );
}


    /**
     * List supplier conversations.
     *
     * Sidebar.
     */
    public function conversations(Request $request)
{
     $conversations =
            $this->supportConversations
            ->execute()
            ->get();




        return response()->json([

            'conversations' =>
            $conversations->map(function ($conversation) {

                $lastMessage = $conversation->lastMessage;

                $adminId = auth()->id();


                $participant =
                    $conversation
                    ->participants
                    ->first(function ($participant) use ($adminId) {

                        return
                            $participant->context_type === User::class
                            &&
                            (int)$participant->context_id === (int)$adminId;
                    });






                return [

                    'id' =>
                    $conversation->id,

                    'header' =>
                    $this->headerService
                        ->resolve($conversation),

                    'last_message' =>
                    $lastMessage?->message,


                    'updated_at' =>
                    $this->dateFormatter->formatConversation(
                        $conversation->updated_at,
                        auth()->user()->timezone ?? config('app.timezone'),
                    ),


                    'unread' =>
                    $participant?->unreadCount() ?? 0,

                    'has_support' =>
                    $conversation->participants
                        ->contains(function ($participant) {

                            return $participant->role === 'support';

                        }),

                ];
            })

        ]);
    }

    public function allConversations(Request $request)
{
    $conversations =
            $this->allConversations
            ->execute()
            ->get();




        return response()->json([

            'conversations' =>
            $conversations->map(function ($conversation) {

                $lastMessage = $conversation->lastMessage;

                $adminId = auth()->id();


                $participant =
                    $conversation
                    ->participants
                    ->first(function ($participant) use ($adminId) {

                        return
                            $participant->context_type === User::class
                            &&
                            (int)$participant->context_id === (int)$adminId;
                    });






                return [

                    'id' =>
                    $conversation->id,

                    'header' =>
                    $this->headerService
                        ->resolve($conversation),

                    'last_message' =>
                    $lastMessage?->message,


                    'updated_at' =>
                    $this->dateFormatter->formatConversation(
                        $conversation->updated_at,
                        auth()->user()->timezone ?? config('app.timezone'),
                    ),


                    'unread' =>
                    $participant?->unreadCount() ?? 0,

                    'has_support' =>
                    $conversation->participants
                        ->contains(function ($participant) {

                            return $participant->role === 'support';

                        }),

                ];
            })

        ]);
    }


    public function noticeConversations(Request $request)
{
     $conversations =
            $this->noticeConversations
            ->execute()
            ->get();




        return response()->json([

            'conversations' =>
            $conversations->map(function ($conversation) {

                $lastMessage = $conversation->lastMessage;

                $adminId = auth()->id();


                $participant =
                    $conversation
                    ->participants
                    ->first(function ($participant) use ($adminId) {

                        return
                            $participant->context_type === User::class
                            &&
                            (int)$participant->context_id === (int)$adminId;
                    });






                return [

                    'id' =>
                    $conversation->id,

                    'header' =>
                    $this->headerService
                        ->resolve($conversation),

                    'last_message' =>
                    $lastMessage?->message,


                    'updated_at' =>
                    $this->dateFormatter->formatConversation(
                        $conversation->updated_at,
                        auth()->user()->timezone ?? config('app.timezone'),
                    ),


                    'unread' =>
                    $participant?->unreadCount() ?? 0,

                    'has_support' =>
                    $conversation->participants
                        ->contains(function ($participant) {

                            return $participant->role === 'support';

                        }),

                ];
            })

        ]);
    }

    /**
     * Open conversation.
     *
     * Used when click sidebar item.
     */
    public function show(Conversation $conversation)
    {


        $currentType = $this->context->type();
        $currentId   = $this->context->id();


        $participant =
    $conversation
        ->participants
        ->first(function($participant) use ($currentType, $currentId) {

            return
                $participant->context_type === $currentType
                &&
                (int)$participant->context_id === (int)$currentId;

        });


if ($participant) {

    $participant->update([
        'last_read_at' => now(),
    ]);

}



        $conversation->load([
            'messages.sender',
            'messages.creator',
        ]);



        $header = $this->headerService->resolve($conversation);




        return response()->json([

            'conversation' => [

                'id' =>
                $conversation->id,

                'type' =>
                $conversation->conversation_type,

                'status' => $conversation->status->value,
                

            ],

            'header' => $header,

            'messages' =>
            $conversation
                ->messages
                ->sortBy('created_at')
                ->map(function ($message) use ($currentType, $currentId) {


               Log::info('Creator', [
    'message_id' => $message->id,
    'creator_by' => $message->created_by,
    'creator_loaded' => $message->relationLoaded('creator'),
    'creator' => $message->creator?->id,
]);


                    return [

                        'id' =>
                        $message->id,


                        'message' =>
                        $message->message,


                        'type' =>
                        $message->message_type,


                        'created_at' =>
                        $this->dateFormatter->formatConversation(
                            $message->created_at,
                            auth()->user()->timezone ?? config('app.timezone'),
                        ),


                        'is_mine' =>
                        $message->sender_type === $currentType &&
                            (int)$message->sender_id === (int)$currentId,


                        'sender' => [

                            'id' =>
                            $message->sender?->id,

                            'name' =>
                            $message->sender_type === \App\Models\User::class
                                ? trim(
                                    ($message->sender?->name ?? '') . ' ' .
                                        ($message->sender?->last_name ?? '')
                                )
                                : $message->sender?->name,

                            'avatar' =>
                            $message->creator?->avatar()?->cdn_url
                                ?? asset('images/default-avatar.png'),

                        ],

                    ];
                }),

        ]);
    }

    public function showAll(Conversation $conversation)
    {


        $currentType = $this->context->type();
        $currentId   = $this->context->id();


        $participant =
    $conversation
        ->participants
        ->first(function($participant) use ($currentType, $currentId) {

            return
                $participant->context_type === $currentType
                &&
                (int)$participant->context_id === (int)$currentId;

        });


if ($participant) {

    $participant->update([
        'last_read_at' => now(),
    ]);

}



        $conversation->load([
            'messages.sender',
            'messages.creator',
        ]);



        $header = $this->headerService->resolve($conversation);




        return response()->json([

            'conversation' => [

                'id' =>
                $conversation->id,

                'type' =>
                $conversation->conversation_type,

                'status' => $conversation->status->value,
                

            ],

            'header' => $header,

            'messages' =>
            $conversation
                ->messages
                ->sortBy('created_at')
                ->map(function ($message) use ($currentType, $currentId) {


               Log::info('Creator', [
    'message_id' => $message->id,
    'creator_by' => $message->created_by,
    'creator_loaded' => $message->relationLoaded('creator'),
    'creator' => $message->creator?->id,
]);


                    return [

                        'id' =>
                        $message->id,


                        'message' =>
                        $message->message,


                        'type' =>
                        $message->message_type,


                        'created_at' =>
                        $this->dateFormatter->formatConversation(
                            $message->created_at,
                            auth()->user()->timezone ?? config('app.timezone'),
                        ),


                        'is_mine' =>
                        $message->sender_type === $currentType &&
                            (int)$message->sender_id === (int)$currentId,


                        'sender' => [

                            'id' =>
                            $message->sender?->id,

                            'name' =>
                            $message->sender_type === \App\Models\User::class
                                ? trim(
                                    ($message->sender?->name ?? '') . ' ' .
                                        ($message->sender?->last_name ?? '')
                                )
                                : $message->sender?->name,

                            'avatar' =>
                            $message->creator?->avatar()?->cdn_url
                                ?? asset('images/default-avatar.png'),

                        ],

                    ];
                }),

        ]);
    }

    public function showNotice(Conversation $conversation)
    {


        $currentType = $this->context->type();
        $currentId   = $this->context->id();


        $participant =
    $conversation
        ->participants
        ->first(function($participant) use ($currentType, $currentId) {

            return
                $participant->context_type === $currentType
                &&
                (int)$participant->context_id === (int)$currentId;

        });


if ($participant) {

    $participant->update([
        'last_read_at' => now(),
    ]);

}



        $conversation->load([
            'messages.sender',
            'messages.creator',
        ]);



        $header = $this->headerService->resolve($conversation);




        return response()->json([

            'conversation' => [

                'id' =>
                $conversation->id,

                'type' =>
                $conversation->conversation_type,

                'status' => $conversation->status->value,
                

            ],

            'header' => $header,

            'messages' =>
            $conversation
                ->messages
                ->sortBy('created_at')
                ->map(function ($message) use ($currentType, $currentId) {


               Log::info('Creator', [
    'message_id' => $message->id,
    'creator_by' => $message->created_by,
    'creator_loaded' => $message->relationLoaded('creator'),
    'creator' => $message->creator?->id,
]);


                    return [

                        'id' =>
                        $message->id,


                        'message' =>
                        $message->message,


                        'type' =>
                        $message->message_type,


                        'created_at' =>
                        $this->dateFormatter->formatConversation(
                            $message->created_at,
                            auth()->user()->timezone ?? config('app.timezone'),
                        ),


                        'is_mine' =>
                        $message->sender_type === $currentType &&
                            (int)$message->sender_id === (int)$currentId,


                        'sender' => [

                            'id' =>
                            $message->sender?->id,

                            'name' =>
                            $message->sender_type === \App\Models\User::class
                                ? trim(
                                    ($message->sender?->name ?? '') . ' ' .
                                        ($message->sender?->last_name ?? '')
                                )
                                : $message->sender?->name,

                            'avatar' =>
                            $message->creator?->avatar()?->cdn_url
                                ?? asset('images/default-avatar.png'),

                        ],

                    ];
                }),

        ]);
    }

    public function markAsRead(Conversation $conversation)
{
    $this->markConversationRead->execute(
        $conversation->id,
        $this->context->type(),
        $this->context->id()
    );

    return response()->json([
        'success' => true,
    ]);
}

public function newMessages(
    Conversation $conversation,
    Request $request
) {



    return response()->json([

        'messages' =>

            $this->loadNewMessages->execute(

                conversation: $conversation,

                currentType: $this->context->type(),

                currentId: $this->context->id(),

                after: (int) $request->get('after', 0),

            ),

    ]);

}

public function deleteEmptyConversations(
    
)
{
    $count = $this->conversationService
        ->deleteEmptyConversations();


    return response()->json([
        'success' => true,
        'count' => $count,
    ]);
}

public function statistics(): JsonResponse
{
    $statistics =
        $this->conversationService
            ->getConversationStatistics();

    return response()->json([
        'total' => $statistics['total'],
        'empty' => $statistics['empty'],
    ]);
}

public function destroyMessage(
    Message $message
): JsonResponse
{
    $this->conversationService
        ->deleteMessage($message);

    return response()->json([
        'success' => true,
    ]);
}

public function close(
    Conversation $conversation
): JsonResponse {

    $this->conversationService
        ->closeConversation($conversation);

    return response()->json([
        'success' => true,
        'conversation' => $conversation,
    ]);
}

public function reopen(
    Conversation $conversation
): JsonResponse
{
    $this->conversationService
        ->reopenConversation($conversation);

    return response()->json([
        'success' => true,
        'conversation' => $conversation,
    ]);

}
public function destroyConversation(
    Conversation $conversation
): JsonResponse
{
    $this->conversationService
        ->deleteConversation($conversation);

    return response()->json([
        'success' => true,
    ]);
}


public function createNotice(
    Request $request,
    CreateNoticeConversationAction $action
)
{
    $data = $request->validate([
        'title' => [
            'required',
            'string',
            'max:255',
        ],

        'subtitle' => [
            'nullable',
            'string',
            'max:255',
        ],

        'description' => [
            'required',
            'string',
            'max:5000',
        ],
    ]);

    $conversation = $action->execute(
        title: $data['title'],
        subtitle: $data['subtitle'] ?? null,
        description: $data['description'],
        createdBy: auth()->id(),
    );

    return response()->json([
    'success' => true,
    'conversation' => [
        'id' => $conversation->id,
    ],
]);
}

}
