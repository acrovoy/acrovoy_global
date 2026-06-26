<?php

namespace App\Http\Controllers\Rfq;

use App\Http\Controllers\Controller;

use App\Domain\RFQ\Actions\Supplier\CreateRfqOfferAction;
use App\Domain\RFQ\DTO\CreateRfqOfferData;
use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Models\RfqOfferVersion;
use App\Domain\Negotiation\Services\OfferDecisionService;
use App\Services\Company\ActiveContextService;
use Illuminate\Http\Request;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\Negotiation\Services\OfferVersionBuilder;
use App\Domain\Negotiation\Actions\OfferVersionItemAutosaveAction;
use App\Domain\Negotiation\Actions\CounterOfferVersionItemAutosaveAction;
use App\Domain\Negotiation\Actions\CreateCounterOfferAction;
use App\Domain\Negotiation\Resolvers\OfferVersionResolver;
use App\Domain\Negotiation\Actions\SubmitOfferVersionAction;
use App\Domain\Negotiation\Actions\SubmitCounterOfferAction;
use App\Domain\Negotiation\Actions\DeleteDraftOfferVersionAction;
use App\Domain\Negotiation\Actions\DeleteCounterOfferDraftAction;
use App\Models\User;



class RfqOfferController extends Controller
{


    public function index(Rfq $rfq)
    {
        $offers = RfqOffer::query()
            ->where('rfq_id', $rfq->id)
            ->with('versions')
            ->latest()
            ->get();

        return view('rfq.offers.index', compact('rfq', 'offers'));
    }


    /**
     * BUYER: accept specific OFFER VERSION (NOT offer itself)
     */
    public function accept(
         Rfq $rfq,
        RfqOffer $offer,
    RfqOfferVersion $version,
    OfferDecisionService $service,
    ActiveContextService $context
    ) {
    
 /**
     * 1. CHECK RFQ OWNERSHIP (SECURITY)
     */
    if (
        $rfq->buyer_type !== $context->type() ||
        $rfq->buyer_id !== $context->id()
    ) {
        abort(403, 'Unauthorized RFQ access');
    }

    /**
     * 2. CHECK OFFER BELONGS TO RFQ
     */
    if ($offer->rfq_id !== $rfq->id) {
        abort(404, 'Offer does not belong to RFQ');
    }

    /**
     * 3. CHECK VERSION BELONGS TO OFFER
     */
    if ($version->	rfq_offer_id !== $offer->id) {
        abort(404, 'Version does not belong to Offer');
    }

    /**
     * 4. BUSINESS LOGIC
     */
    $service->accept(
        $version,
        auth()->id()
    );

    return redirect()
    ->route('buyer.rfqs.offer-comparison', [
        'rfq' => $rfq->id
    ])
    ->with('success', 'Offer version accepted');
}

    /**
     * BUYER: reject whole offer (all versions)
     */
    public function reject(
        RfqOffer $offer,
        OfferDecisionService $service,
        ActiveContextService $context
    ) {
        abort_if($context->role() !== 'buyer', 403);

        $service->reject(
            $offer,
            $context->user()->id
        );

        return back()->with('success', 'Offer rejected');
    }




    public function submitOfferVersion(
    Rfq $rfq,
    RfqOfferVersion $version,
    ActiveContextService $context,
    SubmitOfferVersionAction $action
) {

    if ($context->isGuest()) {
        abort(403);
    }

    $user = $context->user();

   
    /*
    |--------------------------------------------------------------------------
    | PERSONAL
    |--------------------------------------------------------------------------
    */

   

    /*
    |--------------------------------------------------------------------------
    | COMPANY
    |--------------------------------------------------------------------------
    */

  

  

    if ($version->status !== 'draft') {
        abort(422);
    }

    $submittedVersion = $action->execute($version);

    return redirect()->route(
    'rfqs.workspace',
    [
        'rfq' => $rfq->id,
        'tab' => 's-requirements',
        'offer' => $version->rfq_offer_id,
        'version' => $submittedVersion->id,
    ]
)->with(
    'success',
    'Offer submitted successfully.'
);
}






    public function createRevision(Rfq $rfq, ActiveContextService $context)
    {
        if (!$context->isSupplier()) {
            abort(403);
        }

        /**
         * =========================
         * UNIFIED IDENTITY
         * =========================
         */
        $identity = [
            'type' => $context->isCompany()
                ? $context->type()
                : User::class,

            'id' => $context->id(),
        ];

        /**
         * =========================================
         * GET OFFER
         * =========================================
         */
        $offer = \App\Domain\Negotiation\Models\RfqOffer::query()
            ->where('rfq_id', $rfq->id)
            ->where('participant_type', $identity['type'])
            ->where('participant_id', $identity['id'])
            ->firstOrFail();

        /**
         * =========================================
         * GET LATEST VERSION (submitted or draft fallback)
         * =========================================
         */
        $lastVersion = $offer->versions()
            ->where('status', 'submitted')
            ->where('is_counter', 0)
            ->orderByDesc('created_at')
            ->first();

        if (!$lastVersion) {
            abort(404, 'No version to revise');
        }

        /**
         * =========================================
         * CREATE NEW DRAFT VERSION
         * =========================================
         */
        $newVersion = $offer->versions()->create([
            'version_number' => null,
            'status' => 'draft',
            'owner_type' => $identity['type'],
            'owner_id' => $identity['id'],
            'created_by' => $context->user()->id,
        ]);

        /**
         * =========================================
         * CLONE ITEMS
         * =========================================
         */
        foreach ($lastVersion->items as $item) {

            $newItem = $newVersion->items()->create([
                'requirement_id' => $item->requirement_id,
                'attribute_id' => $item->attribute_id,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'currency' => $item->currency,
                'lead_time_days' => $item->lead_time_days,
                'moq' => $item->moq,
                'notes' => $item->notes,

            ]);

            /**
             * =========================================
             * CLONE OPTIONS
             * =========================================
             */
            if ($item->options && $item->options->count()) {

                $newItem->options()->sync(
                    $item->options->pluck('id')->toArray()
                );
            }
        }

        /**
         * =========================================
         * EVENT (optional but recommended)
         * =========================================
         */
    // RfqOfferEvent::create([...]);

        /**
         * =========================================
         * REDIRECT TO EDITOR
         * =========================================
         */
        return redirect()->route('rfqs.workspace', [
            'rfq' => $rfq->id,
            'tab' => 's-requirements',
            'version' => $newVersion->id,
        ]);
    }


    public function createCounterOffer(
        Rfq $rfq,
        RfqOffer $offer,
        ActiveContextService $context,
        CreateCounterOfferAction $action
    ) {
        $buyer = $context->isPersonal()
            ? auth()->user()
            : $context->company();

        if (!$buyer) {
            abort(403);
        }

        $offer->loadMissing(['participant']);

        $result = $action->execute(
            $offer,
            $context->user()->id,
            $context,
            request()->boolean('create')
        );

        $counterVersion = $result['counterVersion'] ?? null;

        return view(
            'rfq.workspace.create-counter-offer',
            array_merge($result, [
                'rfq' => $rfq,
                'offer' => $offer,
                'counter_version' => $counterVersion,
            ])
        );
    }


    public function submitCounterOfferVersion(
    Rfq $rfq,
    RfqOfferVersion $version,
    ActiveContextService $context,
    SubmitCounterOfferAction $action
) {

    if ($context->isGuest()) {
        abort(403);
    }

    $user = $context->user();

    /*
    |--------------------------------------------------------------------------
    | OWNERSHIP CHECK
    |--------------------------------------------------------------------------
    */

   

    /*
    |--------------------------------------------------------------------------
    | EXECUTE
    |--------------------------------------------------------------------------
    */

    $submitted = $action->execute($version);

    return redirect()->route(
        'rfqs.workspace',
        [
            'rfq' => $version->offer->rfq_id,
            'tab' => 'offers',
            'offer' => $version->rfq_offer_id,
            'version' => $submitted->id,
        ]
    )->with('success', 'Counter offer submitted.');
}



    public function autosave(
        Rfq $rfq,
        Request $request,
        OfferVersionItemAutosaveAction $action,
        ActiveContextService $context
    ) {
        

        return $action->execute(
            rfq: $rfq,
            request: $request,
            context: $context
        );
    }


    public function buyerCounterAutosave(
        Rfq $rfq,
        RfqOffer $offer,
        RfqOfferVersion $version,
        Request $request,
        CounterOfferVersionItemAutosaveAction $action,
        ActiveContextService $context
    ) {
        abort_unless($version->rfq_offer_id === $offer->id, 403);
        abort_unless($version->status === 'draft', 403);
        abort_unless($version->is_counter, 403);

        return $action->execute(
            version: $version,
            request: $request,
            context: $context
        );
    }

    public function deleteDraftVersion(
    Rfq $rfq,
    RfqOfferVersion $version,
    ActiveContextService $context,
    DeleteDraftOfferVersionAction $action
) {

    /*
    |--------------------------------------------------------------------------
    | AUTH CHECK
    |--------------------------------------------------------------------------
    */

    if ($context->isGuest()) {
        abort(403);
    }

    $user = $context->user();

    $isOwner = false;

   

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    $offerId = $version->rfq_offer_id;

    $action->execute($version);

    /*
    |--------------------------------------------------------------------------
    | REDIRECT BACK TO WORKSPACE
    |--------------------------------------------------------------------------
    */

    return redirect()->route(
        'rfqs.workspace',
        [
            'rfq' => $version->offer->rfq_id,
            'tab' => 's-requirements',
            'offer' => $offerId,
        ]
    )->with('success', 'Draft deleted successfully.');
}


public function deleteDraftCounterOfferVersion(
    Rfq $rfq,
    RfqOffer $offer,
    RfqOfferVersion $version,
    ActiveContextService $context,
    DeleteCounterOfferDraftAction $action
) {
    abort_unless($version->rfq_offer_id === $offer->id, 403);

    $action->execute($version, $context);

    return redirect()
        ->route('rfqs.workspace', [
            'rfq' => $offer->rfq_id,
            'tab' => 'offers',
            'offer' => $offer->id,
        ])
        ->with('success', 'Draft counter offer deleted');
}


public function comparison(Rfq $rfq)
{
    $rfq->load([
        'attributeValues.attribute.options',
        'attributeValues.options',
        'offers.participant',
        'offers.versions.items.options',
    ]);

    $offers = $rfq->offers->filter(function ($offer) {

        return $offer->versions
            ->whereIn('status', ['submitted', 'accepted', 'rejected'])
            ->isNotEmpty();
    });

    return view('rfq.workspace.offer-comparison', [
        'rfq' => $rfq,
        'offers' => $offers,
    ]);
}


}
