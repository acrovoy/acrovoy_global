<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\DTO\CreateConversationData;
use App\Domain\Conversation\Models\Conversation;
use Illuminate\Support\Facades\Log;

use App\Domain\Conversation\Enums\ConversationStatus;

class FindConversationAction
{
    /**
     * Найти существующий Conversation.
     */
    public function execute(CreateConversationData $data): ?Conversation
    {
        // Log::info('===== FIND CONVERSATION START =====', [

        //     'conversation_type' => $data->conversationType->value,

        //     'subject_type' => $data->subjectType,
        //     'subject_id'   => $data->subjectId,

        //     'context_type' => $data->contextType,
        //     'context_id'   => $data->contextId,

        //     'platform_role' => $data->platformRole,

        // ]);

        $query = Conversation::query()
            ->where('conversation_type', $data->conversationType);

        /*
        |--------------------------------------------------------------------------
        | Business Conversation
        |--------------------------------------------------------------------------
        */

        if ($data->conversationType->value === 'business') {

            $query
                ->where('subject_type', $data->subjectType)
                ->where('subject_id', $data->subjectId);

            if (
                $data->contextType &&
                $data->contextId
            ) {

                $query->whereHas(
                    'participants',
                    function ($participantQuery) use ($data) {

                        $participantQuery
                            ->where('context_type', $data->contextType)
                            ->where('context_id', $data->contextId);

                        if ($data->platformRole) {

                            $participantQuery->where(
                                'platform_role',
                                $data->platformRole
                            );
                        }
                    }
                );
            }

            // Log::info('BUSINESS SQL', [

            //     'sql' => $query->toSql(),
            //     'bindings' => $query->getBindings(),

            // ]);

            $conversation = $query->first();

            // Log::info('BUSINESS RESULT', [

            //     'found' => (bool) $conversation,

            //     'conversation_id' => $conversation?->id,

            //     'conversation_type' => $conversation?->conversation_type,

            //     'subject_type' => $conversation?->subject_type,
            //     'subject_id' => $conversation?->subject_id,

            // ]);

            return $conversation;
        }

        /*
|--------------------------------------------------------------------------
| Private Conversation
|--------------------------------------------------------------------------
*/

        if ($data->conversationType->value === 'private') {

            $query
                ->where('subject_type', $data->subjectType)
                ->where('subject_id', $data->subjectId)

                ->whereHas('participants', function ($q) use ($data) {

                    $q->where('context_type', $data->contextType)
                        ->where('context_id', $data->contextId);

                    if ($data->platformRole) {
                        $q->where('platform_role', $data->platformRole);
                    }
                })

                ->whereHas('participants', function ($q) use ($data) {

                    $q->where('context_type', $data->subjectType)
                        ->where('context_id', $data->subjectId);
                });

            // Log::info('PRIVATE SQL', [

            //     'sql' => $query->toSql(),
            //     'bindings' => $query->getBindings(),

            // ]);

            $conversation = $query->first();

            if (
                $conversation &&
                $conversation->status !== ConversationStatus::ACTIVE
            ) {
                return null;
            }

            return $conversation;

            // Log::info('PRIVATE RESULT', [

            //     'found' => (bool) $conversation,
            //     'conversation_id' => $conversation?->id,
            //     'conversation_type' => $conversation?->conversation_type,
            //     'subject_type' => $conversation?->subject_type,
            //     'subject_id' => $conversation?->subject_id,

            // ]);

            return $conversation;
        }

        Log::info('Conversation not found (unsupported type).');

        return null;
    }
}
