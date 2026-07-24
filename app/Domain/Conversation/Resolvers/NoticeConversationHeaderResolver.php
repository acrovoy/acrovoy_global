<?php

namespace App\Domain\Conversation\Resolvers;

use App\Domain\Conversation\Contracts\ConversationHeaderResolver;
use App\Domain\Conversation\Enums\ConversationType;
use App\Domain\Conversation\Models\Conversation;

class NoticeConversationHeaderResolver implements ConversationHeaderResolver
{
    public function supports(
        Conversation $conversation
    ): bool {

        return $conversation->conversation_type === ConversationType::NOTICE;

    }

    public function resolve(
        Conversation $conversation
    ): array {

        return [

            /*
            |--------------------------------------------------------------------------
            | Notice
            |--------------------------------------------------------------------------
            */

            'title' => $conversation->title,

            'subtitle' => $conversation->subtitle,

            'avatar' => asset('images/notice_avatar.png'),

            'url' => null,

            /*
            |--------------------------------------------------------------------------
            | Sender
            |--------------------------------------------------------------------------
            */

            'manager' => [

                'id' => null,

                'name' => 'System Notifications',

                'avatar' => asset('images/notice_avatar.png'),

                'position' => 'Notification Center',

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