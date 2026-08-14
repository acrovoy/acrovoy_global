<?php

namespace App\Domain\Conversation\Resolvers;

use App\Domain\Conversation\Contracts\ConversationHeaderResolver;
use App\Domain\Conversation\Models\Conversation;
use App\Domain\Conversation\Enums\ConversationType;
use App\Models\Buyer;

class BuyerHeaderResolver implements ConversationHeaderResolver
{
    public function supports(Conversation $conversation): bool
    {
        return $conversation->subject_type === Buyer::class;
    }

    public function resolve(Conversation $conversation): array
    {
        /*
        |--------------------------------------------------------------------------
        | Support Chat
        |--------------------------------------------------------------------------
        |
        | Если текущий пользователь — Buyer и в conversation
        | есть Admin, показываем ACROVOY вместо Buyer.
        |
        */

        if ($conversation->conversation_type === ConversationType::BUSINESS) {

            $me = $conversation->participants
                ->first(fn ($participant) =>
                    $participant->actor_id === auth()->id()
                );

            $isAdmin = $me?->platform_role === 'admin';

            $adminParticipant = $conversation->participants
                ->first(fn ($participant) =>
                    $participant->platform_role === 'admin'
                );

            if (!$isAdmin && $adminParticipant) {

                return [

                    'title' => 'ACROVOY',

                    'subtitle' => 'Customer Service',

                    'avatar' =>
                        asset('images/support_avatar.png'),

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
        | Buyer
        |--------------------------------------------------------------------------
        */

        $buyer = Buyer::query()->findOrFail(
            $conversation->subject_id
        );

        return [

            'title' => $buyer->name,

            'subtitle' => $buyer->email,

            'avatar' =>
                $buyer->logo()?->cdn_url
                ?? asset('images/default-avatar.png'),

            'url' => '',

            'manager' => [

                'id' => null,

                'name' => null,

                'avatar' => null,

                'position' => null,

            ],

            'company' => [

                'id' => $buyer->id,

                'name' => $buyer->name,

                'logo' =>
                    $buyer->logo
                    ?? null,

            ],

            'online' => false,

            'last_seen' => null,

        ];
    }
}