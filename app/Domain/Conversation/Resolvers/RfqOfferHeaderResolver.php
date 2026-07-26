<?php

namespace App\Domain\Conversation\Resolvers;

use App\Domain\Conversation\Contracts\ConversationHeaderResolver;
use App\Domain\Conversation\Models\Conversation;
use App\Domain\Negotiation\Models\RfqOffer;
use App\Services\Company\ActiveContextService;

use Illuminate\Support\Facades\Log;

class RfqOfferHeaderResolver implements ConversationHeaderResolver
{
    public function supports(Conversation $conversation): bool
    {
        return $conversation->subject_type === RfqOffer::class;
    }

    public function resolve(Conversation $conversation): array
    {
        $context = app(ActiveContextService::class);

        $isBuyer = $context->platformRole() === 'buyer';

        $rfqOffer = RfqOffer::query()
            ->with([
                'participant',
                'rfq.buyer',
                'rfq.project',
            ])
            ->findOrFail($conversation->subject_id);

        $rfq = $rfqOffer->rfq;

        /*
        |--------------------------------------------------------------------------
        | Собеседник
        |--------------------------------------------------------------------------
        |
        | Buyer -> показываем Supplier
        | Supplier -> показываем Buyer
        |
        */

        $contact = $isBuyer
            ? $rfqOffer->participant
            : $rfq->buyer;

        /*
        |--------------------------------------------------------------------------
        | Универсальное имя
        |--------------------------------------------------------------------------
        */

        $contactName =
            $contact?->company_name
            ?? $contact?->supplier_name
            ?? $contact?->name
            ?? 'Unknown';

            $avatar = null;

if ($contact instanceof \App\Models\User) {

    $avatar = $contact->avatar()?->cdn_url;

} elseif ($contact instanceof \App\Models\Supplier) {

    $avatar = $contact->logo()?->cdn_url;
}


            Log::info('RFQ Offer Header Resolver', [
                'contact' => $contact,

    'conversation_id' => $conversation->id,

    'platform_role' => $context->platformRole(),

    'contact_class' => $contact ? get_class($contact) : null,

    'contact_id' => $contact?->id,

    'contact_company_name' => $contact?->company_name ?? null,

    'contact_name' => $contact?->name ?? null,

    'avatar_url' => $contact?->avatar_url ?? null,

    'logo_url' => $contact?->logo_url ?? null,

    'contact_attributes' => $contact?->getAttributes(),

    'contact_array' => $contact?->toArray(),

]);


if($rfq->project){

$title = $rfq->project->title;
$subtitle = $rfq->project->public_id;
$label = 'View Project';

if($isBuyer) {
    $url = route('buyer.projects.show', $rfq->project->id);
} else {
    $url = route('supplier.projects.show', $rfq->project->id);
}


} else {

$title = $rfq->title;
$subtitle = $rfq->public_id;
$label = 'View RFQ';
$url = route('rfqs.workspace', $rfq->id);

}



        return [

            /*
            |--------------------------------------------------------------------------
            | RFQ
            |--------------------------------------------------------------------------
            */

            'title' => $title,

            'subtitle' => $subtitle,

            'label' => $label,

            'avatar' => $avatar,

            /*
            |--------------------------------------------------------------------------
            | Link
            |--------------------------------------------------------------------------
            */

            'url' => $url,

            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            'manager' => [

                'id' => $contact?->id,

                'name' => $contactName,

                'avatar' => $contact?->avatar_url
                    ?? $contact?->logo_url
                    ?? null,

                'position' => null,

            ],

            /*
            |--------------------------------------------------------------------------
            | Company
            |--------------------------------------------------------------------------
            */

            'company' => [

                'id' => $contact?->id,

                'name' => $contactName,

                'logo' => $contact?->logo_url
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