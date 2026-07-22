<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Conversation\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

use App\Domain\Conversation\Queries\SupplierConversationsQuery;

use App\Domain\Conversation\Actions\RequestSupportAction;
use App\Domain\Conversation\Actions\MarkConversationReadAction;
use App\Domain\Conversation\Actions\LoadNewMessagesAction;
use App\Services\Date\UserDateFormatter;


use App\Services\Company\ActiveContextService;

use App\Domain\Conversation\Services\ConversationHeaderService;

class SupplierMessengerController extends Controller
{

public function __construct(
    private SupplierConversationsQuery $supplierConversations,
    private ActiveContextService $context,
    private ConversationHeaderService $headerService,
    private MarkConversationReadAction $markConversationRead,
    private RequestSupportAction $requestSupportAction,
    private LoadNewMessagesAction $loadNewMessages,
    private UserDateFormatter $dateFormatter,
)
{

}
    /**
     * Messenger page.
     */
    public function index()
    {
        return view(
            'dashboard.supplier.messenger.index'
        );
    }


    /**
     * List supplier conversations.
     *
     * Sidebar.
     */
    public function conversations()
{

    $supplierId =
        $this->context->id();

    $supplierType =
        $this->context->type();


    $conversations =
        $this->supplierConversations
            ->execute($supplierType, $supplierId)
            ->get();


    return response()->json([

        'conversations' =>
            $conversations->map(function ($conversation) use ($supplierType, $supplierId) {

                $lastMessage = $conversation->lastMessage;

                $participant =
                    $conversation
                        ->participants
                        ->first(function ($participant) use ($supplierType, $supplierId) {

                            return
                                $participant->context_type === $supplierType
                                && (int) $participant->context_id === (int) $supplierId;

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





    $conversation->load([
        'messages.sender',
        'messages.creator',
        'participants',
    ]);

   



    $header = $this->headerService->resolve($conversation);

   
   

        return response()->json([

            'conversation' => [

                'id' =>
                    $conversation->id,

                'type' =>
                    $conversation->conversation_type,

            ],

             'header' => $header,

             'has_support' =>
                $conversation->participants()
                    ->where('role', 'support')
                    ->exists(),

            'messages' =>
                $conversation
                    ->messages
                    ->sortBy('created_at')
                    ->map(function ($message) use ($currentType, $currentId) {

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

    /**
 * Mark conversation as read.
 */
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

public function requestSupport(
    Request $request,
    Conversation $conversation
) {


    $request->validate([
        'reason' => ['nullable', 'string', 'max:1000'],
    ]);

    $message = $this->requestSupportAction->execute(

        conversation: $conversation,

        requesterType: $this->context->type(),

        requesterId: $this->context->id(),

        reason: $request->input('reason')

    );

    return response()->json([

        'success' => true,

        'message' => [

            'id' => $message->id,

            'message' => $message->message,

            'type' => $message->message_type,

            'created_at' =>
            $this->dateFormatter->formatConversation(
                $message->created_at,
                auth()->user()->timezone ?? config('app.timezone'),
            ),

            'is_mine' => true,

            'sender' => [

                'id' => auth()->id(),

                'name' => 'Acrovoy System',

                'avatar' => asset('images/support_avatar.png'),

                'position' => 'System',

                'company' => '',

            ],

        ],

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

}