<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Company\ActiveContextService;
use App\Domain\Conversation\Actions\CreateSupportRequestAction;
use Illuminate\Support\Facades\Log;
use App\Domain\Conversation\Services\ConversationHeaderService;


class SupportRequestController extends Controller
{
    public function __construct(
        private ActiveContextService $context,
        private CreateSupportRequestAction $action
    ) {}

    public function store(Request $request)
{
    Log::info('========== SUPPORT REQUEST ==========');

    $identity = $this->context->identity();

    Log::info('Identity', $identity);

    Log::info('Request payload', $request->all());

    $data = $request->validate([

        'subject' => [
            'required',
            'string',
            'max:150',
        ],

        'category' => [
            'nullable',
            'string',
        ],

        'description' => [
            'required',
            'string',
            'max:2000',
        ],

    ]);

    Log::info('Validated data', $data);

    $conversation = $this->action->execute(

        requesterType: $identity['entity_type'],
        requesterId: $identity['entity_id'],
        requesterPlatformRole: $identity['platform_role'],

        subject: $data['subject'],
        category: $data['category'] ?? null,
        description: $data['description'],

    );

    Log::info('Conversation created', [
        'conversation_id' => $conversation->id,
    ]);

    $conversation->load([
        'messages.sender',
        'messages.creator',
    ]);

    Log::info('Conversation loaded');

    return response()->json([

        'success' => true,

        'conversation' => [
            'id' => $conversation->id,
        ],

        'header' => app(ConversationHeaderService::class)
            ->resolve($conversation),

        'has_support' => true,

    ]);
}
}
