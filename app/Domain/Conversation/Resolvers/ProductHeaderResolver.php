<?php

namespace App\Domain\Conversation\Resolvers;

use App\Domain\Conversation\Contracts\ConversationHeaderResolver;
use App\Domain\Conversation\Models\Conversation;
use App\Models\Product;

class ProductHeaderResolver implements ConversationHeaderResolver
{
    public function supports(string $subjectType): bool
    {
        return $subjectType === Product::class;
    }

    public function resolve(Conversation $conversation): array
    {
        $product = Product::query()
            ->with([
                'translations',
                'images',
                'supplier',
            ])
            ->findOrFail($conversation->subject_id);

        return [

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */

            'title' => $product->name,

            'subtitle' => $product->undername,

            'avatar' => $product->main_image_url,

            'url' => route('product.show', $product->slug),

            /*
            |--------------------------------------------------------------------------
            | Manager
            |--------------------------------------------------------------------------
            |
            | TODO:
            | Получать ответственного менеджера поставщика.
            |
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

                'id' => $product->supplier?->id,

                'name' => $product->supplierName(),

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