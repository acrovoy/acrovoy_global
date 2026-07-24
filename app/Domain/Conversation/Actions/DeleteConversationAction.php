<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Conversation;
use Illuminate\Support\Facades\DB;

class DeleteConversationAction
{
    public function execute(
        Conversation $conversation
    ): void {

        DB::transaction(function () use ($conversation) {

            /*
            |--------------------------------------------------------------------------
            | Delete message attachments (if relationship exists)
            |--------------------------------------------------------------------------
            */

            // foreach ($conversation->messages as $message) {

            //     if (method_exists($message, 'attachments')) {
            //         $message->attachments()->delete();
            //     }

            // }

            /*
            |--------------------------------------------------------------------------
            | Delete messages
            |--------------------------------------------------------------------------
            */

            $conversation
                ->messages()
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Delete participants
            |--------------------------------------------------------------------------
            */

            $conversation
                ->participants()
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Delete conversation
            |--------------------------------------------------------------------------
            */

            $conversation->delete();

        });

    }
}