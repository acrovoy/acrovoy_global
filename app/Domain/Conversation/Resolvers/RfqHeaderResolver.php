<?php

namespace App\Domain\Conversation\Resolvers;

use App\Domain\Conversation\Contracts\ConversationHeaderResolver;
use App\Domain\Conversation\Models\Conversation;
use App\Domain\RFQ\Models\Rfq;

class RfqHeaderResolver implements ConversationHeaderResolver
{
    public function supports(Conversation $conversation): bool
    {
        return $conversation->subject_type === Rfq::class;
    }

    public function resolve(Conversation $conversation): array
    {
        return [

            'title' => 'RFQ',

            'subtitle' => null,

            'avatar' => null,

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

            'online' => false,

            'last_seen' => null,

        ];
    }
}