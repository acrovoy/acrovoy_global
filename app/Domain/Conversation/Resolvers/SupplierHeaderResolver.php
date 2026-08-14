<?php

namespace App\Domain\Conversation\Resolvers;

use App\Domain\Conversation\Contracts\ConversationHeaderResolver;
use App\Domain\Conversation\Models\Conversation;
use App\Models\Supplier;

class SupplierHeaderResolver implements ConversationHeaderResolver
{
    public function supports(Conversation $conversation): bool
    {
        return $conversation->subject_type === Supplier::class;
    }

    public function resolve(Conversation $conversation): array
    {
        $supplier = Supplier::query()->findOrFail(
            $conversation->subject_id
        );

        return [

            'title' => $supplier->name,

            'subtitle' => $supplier->email,

            'avatar' =>
                $supplier->logo
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

                'id' => $supplier->id,

                'name' => $supplier->name,

                'logo' =>
                    $supplier->logo
                    ?? null,

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