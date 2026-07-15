<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Conversation\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

use App\Domain\Conversation\Queries\SupplierConversationsQuery;
use App\Services\Company\ActiveContextService;

use App\Domain\Conversation\Services\ConversationHeaderService;

class SupplierMessengerController extends Controller
{

public function __construct(
    private SupplierConversationsQuery $supplierConversations,
    private ActiveContextService $context,
    private ConversationHeaderService $headerService,
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
            $conversations->map(function($conversation){


                $lastMessage = $conversation->lastMessage;



                return [

                    'id'=>
                        $conversation->id,
                        
                    'header' =>
                                $this->headerService
                                    ->resolve($conversation),

                    'last_message'=>
                        $lastMessage?->message,


                    'updated_at'=>
                        $conversation
                        ->updated_at
                        ?->format('Y-m-d H:i'),


                    'unread'=>
                        0,

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
                                $message->created_at
                                    ?->format('H:i'),


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
}