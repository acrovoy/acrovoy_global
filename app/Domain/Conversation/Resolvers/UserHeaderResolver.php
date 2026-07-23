<?php

namespace App\Domain\Conversation\Resolvers;

use App\Domain\Conversation\Contracts\ConversationHeaderResolver;
use App\Domain\Conversation\Enums\ConversationType;
use App\Domain\Conversation\Models\Conversation;
use App\Models\User;

class UserHeaderResolver implements ConversationHeaderResolver
{
    public function supports(Conversation $conversation): bool
    {
        return $conversation->subject_type === User::class;
    }

    public function resolve(Conversation $conversation): array
    {
        /*
        |--------------------------------------------------------------------------
        | Support Chat
        |--------------------------------------------------------------------------
        |
        | Если пользователь общается с поддержкой,
        | вместо имени администратора показываем Support.
        |
        */

        if ($conversation->conversation_type === ConversationType::PRIVATE) {

            $me = $conversation->participants
                ->first(fn ($participant) => $participant->actor_id === auth()->id());

            $isAdmin = $me?->platform_role === 'admin';

            $adminParticipant = $conversation->participants
                ->first(fn ($participant) => $participant->platform_role === 'admin');

            if (!$isAdmin && $adminParticipant) {

                return [

                    'title' => 'ACROVOY',

                    'subtitle' => 'Customer Service',

                    'avatar' => asset('images/support_avatar.png'),

                    'url' => '',

                    'manager' => [

                        'id' => null,

                        'name' => null,

                        'avatar' => null,

                        'position' => null,

                    ],

                    'company' => [

                        'id' => null,

                        'name' => null,

                        'logo' => null,

                    ],

                    'online' => true,

                    'last_seen' => null,

                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        $user = User::query()->findOrFail($conversation->subject_id);

        return [

            'title' => trim(
                ($user->name ?? '') . ' ' .
                ($user->last_name ?? '')
            ),

            'subtitle' => $user->email,

            'avatar' =>
                $user->avatar()?->cdn_url
                ?? asset('images/default-avatar.png'),

            'url' => '',

            /*
            |--------------------------------------------------------------------------
            | Manager
            |--------------------------------------------------------------------------
            */

            'manager' => [

                'id' => null,

                'name' => null,

                'avatar' => null,

                'position' => null,

            ],

            /*
            |--------------------------------------------------------------------------
            | Company
            |--------------------------------------------------------------------------
            */

            'company' => [

                'id' => null,

                'name' => null,

                'logo' => null,

            ],

            /*
            |--------------------------------------------------------------------------
            | Presence
            |--------------------------------------------------------------------------
            */

            'online' => false,

            'last_seen' => null,

        ];
    }
}