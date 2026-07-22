<?php

namespace App\Domain\Conversation\Resolvers;

use App\Domain\Conversation\Contracts\ConversationHeaderResolver;
use App\Domain\Conversation\Models\Conversation;

use App\Domain\Conversation\Enums\ConversationType;

class SupportConversationHeaderResolver implements ConversationHeaderResolver
{
    public function supports(
        Conversation $conversation
    ): bool {

        return $conversation->conversation_type === ConversationType::SUPPORT;

    }

    public function resolve(
        Conversation $conversation
    ): array {

        return [

            /*
            |--------------------------------------------------------------------------
            | Support Request
            |--------------------------------------------------------------------------
            */

            'title' => $conversation->title,

            'subtitle' => $conversation->subtitle,

            'avatar' => asset('images/support_avatar.png'),

            'url' => null,

            /*
            |--------------------------------------------------------------------------
            | Support Agent
            |--------------------------------------------------------------------------
            */

            'manager' => [

                'id' => null,

                'name' => 'Support Team',

                'avatar' => asset('images/support_avatar.png'),

                'position' => 'Customer Support',

            ],

            /*
            |--------------------------------------------------------------------------
            | Company
            |--------------------------------------------------------------------------
            */

            'company' => [

                'id' => null,

                'name' => config('app.name'),

                'logo' => null,

            ],

            /*
            |--------------------------------------------------------------------------
            | Presence
            |--------------------------------------------------------------------------
            */

            'online' => true,

            'last_seen' => null,

        ];
    }
}