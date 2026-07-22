<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\Models\Conversation;

class LoadNewMessagesAction
{
    public function __construct(
        private FormatConversationMessageAction $formatMessage,
    ) {
    }

    public function execute(
        Conversation $conversation,
        string $currentType,
        int $currentId,
        int $after = 0,
    ) {
        $timezone = auth()->user()?->timezone
            ?? config('app.timezone');

        return $conversation
            ->messages()
            ->where('id', '>', $after)

            // не возвращаем сообщения текущего участника
            ->where(function ($query) use ($currentType, $currentId) {

                $query
                    ->where('sender_type', '!=', $currentType)
                    ->orWhere('sender_id', '!=', $currentId);

            })

            ->with([
                'sender',
                'creator',
            ])

            ->orderBy('id')
            ->get()

            ->map(fn ($message) => $this->formatMessage->execute(
                $message,
                $timezone,
                $currentType,
                $currentId,
            ));
    }
}