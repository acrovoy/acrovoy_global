<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Conversation;
use Illuminate\Support\Facades\DB;

class DeleteEmptyConversationsAction
{
    /**
     * Удалить все беседы без сообщений.
     */
    public function execute(): int
    {
        return DB::transaction(function () {

            $conversations = Conversation::query()
                ->whereDoesntHave('messages')
                ->get();


            foreach ($conversations as $conversation) {

                $conversation->participants()->delete();

                $conversation->delete();

            }


            return $conversations->count();

        });
    }
}