<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Conversation\DTO\CreateConversationData;
use App\Domain\Conversation\Models\Conversation;

class FindConversationAction
{
    /**
     * Найти существующий Conversation.
     */
    public function execute(CreateConversationData $data): ?Conversation
    {

    
        $query = Conversation::query()
            ->where('conversation_type', $data->conversationType);


        /*
        |----------------------------------------------------------------------
        | Business Conversation
        |----------------------------------------------------------------------
        |
        | Business conversation всегда привязана к subject:
        |
        | Product
        | RFQ
        | Project
        |
        | Но одного subject недостаточно.
        |
        | Например:
        |
        | Buyer A -> Product X -> Supplier B
        |
        | и
        |
        | Buyer C -> Product X -> Supplier B
        |
        | должны иметь разные Conversation.
        |
        | Поэтому дополнительно проверяем участников.
        |
        */
        if ($data->conversationType->value === 'business') {


            $query
                ->where('subject_type', $data->subjectType)
                ->where('subject_id', $data->subjectId);


            /*
            |--------------------------------------------------------------------------
            | Проверяем текущий контекст участника
            |--------------------------------------------------------------------------
            |
            | Conversation должна принадлежать именно этому контексту.
            |
            | Например:
            |
            | Buyer company A
            | User B
            | Supplier C
            |
            | не должны попадать в чужой диалог.
            |
            */
            if (
                $data->contextType &&
                $data->contextId
            ) {

                $query->whereHas(
                    'participants',
                    function ($participantQuery) use ($data) {

                        $participantQuery
                            ->where(
                                'context_type',
                                $data->contextType
                            )
                            ->where(
                                'context_id',
                                $data->contextId
                            );

                        if ($data->platformRole) {

                            $participantQuery->where(
                                'platform_role',
                                $data->platformRole
                            );
                        }
                    }
                );
            }


            return $query->first();
        }



        /*
        |----------------------------------------------------------------------
        | Private Conversation
        |----------------------------------------------------------------------
        |
        | Пока не реализовано.
        |
        | В дальнейшем здесь будет поиск личного диалога
        | между двумя пользователями или двумя контекстами.
        |
        | Например:
        |
        | User A <-> User B
        |
        | или
        |
        | Company A <-> Company B
        |
        */
        return null;
    }
}
