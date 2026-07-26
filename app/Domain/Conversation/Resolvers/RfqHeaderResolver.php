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
        $rfq = Rfq::query()
            ->with([
                'buyer',
                'offers',
            ])
            ->findOrFail($conversation->subject_id);


        return [

            /*
            |--------------------------------------------------------------------------
            | RFQ
            |--------------------------------------------------------------------------
            */

            'title' => $rfq->title,

            'subtitle' => $rfq->public_id,

            'label' => 'View RFQ',

            'avatar' => asset('images/rfq_avatar.png'),


            /*
            |--------------------------------------------------------------------------
            | Link
            |--------------------------------------------------------------------------
            */

            'url' => route(
    'rfqs.workspace',
    $rfq->id
            ),


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

                'id' => $rfq->buyer?->id,

                'name' => $rfq->buyer?->company_name
                    ?? $rfq->buyer?->name,

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