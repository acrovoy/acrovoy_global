<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Message;
use App\Models\User;

class FormatConversationMessageAction
{
    public function execute(
        Message $message,
        string $timezone,
        string $currentType,
        int $currentId
    ): array {

        $isMine =
            $message->sender_type === $currentType
            &&
            (int)$message->sender_id === (int)$currentId;

        return [

            'id' =>
                $message->id,

            'message' =>
                $message->message,

            'type' =>
                $message->message_type,

            /*
            |--------------------------------------------------------------------------
            | Time in user timezone
            |--------------------------------------------------------------------------
            */

            'created_at' =>
                $message->created_at
                    ?->copy()
                    ->timezone($timezone)
                    ->format('H:i'),

            'is_mine' =>
                $isMine,

            'sender' => [

                'id' =>
                    $message->sender?->id,

                'name' =>
                    $message->creator?->role === 'admin'
                        ? 'ACROVOY'
                        : match ($message->sender_type) {

                            User::class => trim(
                                ($message->sender?->name ?? '') . ' ' .
                                ($message->sender?->last_name ?? '')
                            ),

                            default => $message->sender?->name,
                        },

                /*
                |--------------------------------------------------------------------------
                | Avatar comes from creator user
                |--------------------------------------------------------------------------
                */

                'avatar' =>
                    $message->creator?->avatar()?->cdn_url
                    ?? asset('images/default-avatar.png'),

                'position' =>
                    $message->sender?->position,

                'company' =>
                    $message->sender?->company?->name,

            ],

            /*
            |--------------------------------------------------------------------------
            | Attachments placeholder
            |--------------------------------------------------------------------------
            */

            'attachments' => [],

        ];
    }
}