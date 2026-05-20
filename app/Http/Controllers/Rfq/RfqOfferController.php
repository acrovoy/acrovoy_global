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
use App\Domain\Negotiation\Resolvers\OfferVersionResolver;

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
        RfqOfferVersion $version,
        OfferDecisionService $service,
        ActiveContextService $context
    ) {


        $buyerType = $context->isPersonal()
            ? \App\Models\User::class
            : \App\Models\Buyer::class;

        $buyer = $context->isPersonal()
            ? auth()->user()->id
            : $context->company()->id;

        $service->accept(
            $version,
            $buyer
        );

        return back()->with('success', 'Offer version accepted');
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







    public function createRevision(Rfq $rfq, ActiveContextService $context)
    {
        $supplier = $context->supplier();

        if (!$supplier) {
            abort(403);
        }

        /**
         * =========================================
         * GET OFFER
         * =========================================
         */
        $offer = \App\Domain\Negotiation\Models\RfqOffer::query()
            ->where('rfq_id', $rfq->id)
            ->where('participant_type', get_class($supplier))
            ->where('participant_id', $supplier->id)
            ->firstOrFail();

        /**
         * =========================================
         * GET LATEST VERSION (submitted or draft fallback)
         * =========================================
         */
        $lastVersion = $offer->versions()
            ->orderByDesc('version_number')
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
            'version_number' => $lastVersion->version_number + 1,
            'status' => 'draft',
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
        OfferVersionResolver $resolver,
    ) {
        $buyer = $context->isPersonal()
            ? auth()->user()
            : $context->company();

        if (!$buyer) {
            abort(403);
        }

        $offer->loadMissing([
            'versions.items.options',
            'participant',
        ]);

        /*
    |--------------------------------------------------------------------------
    | LAST SUPPLIER VERSION
    |--------------------------------------------------------------------------
    */



        $lastVersion = $resolver->lastSupplierVersion($offer);

        if (!$lastVersion) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | COUNTER VERSIONS
    |--------------------------------------------------------------------------
    */

        $draftCounter = $resolver->latestCounterVersion(
            $offer,
            $context->user()->id,
            'draft'
        );

        $submittedCounter = $resolver->latestCounterVersion(
            $offer,
            $context->user()->id,
            'submitted'
        );

        /*
    |--------------------------------------------------------------------------
    | CASE 1: DRAFT EXISTS → EDIT IT
    |--------------------------------------------------------------------------
    */

        if ($draftCounter) {

            $versions = $offer->versions()
                ->with(['items.options'])
                ->orderByDesc('version_number')
                ->get();

            $counterItemsByAttribute = $draftCounter->items
                ->load('options')
                ->keyBy('attribute_id');

            return view('rfq.workspace.create-counter-offer', [
                'rfq' => $rfq,
                'offer' => $offer,
                'offerVersion' => $lastVersion,
                'counterVersion' => $draftCounter,
                'itemsByAttribute' => $lastVersion->items->keyBy('attribute_id'),
                'counterItemsByAttribute' => $counterItemsByAttribute,
                'versions' => $versions,
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | CASE 2: SUBMITTED EXISTS → READ ONLY
    |--------------------------------------------------------------------------
    */

        if ($submittedCounter) {

            $versions = $offer->versions()
                ->with(['items.options'])
                ->orderByDesc('version_number')
                ->get();

            $counterItemsByAttribute = $submittedCounter->items
                ->load('options')
                ->keyBy('attribute_id');

            return view('rfq.workspace.create-counter-offer', [
                'rfq' => $rfq,
                'offer' => $offer,
                'offerVersion' => $lastVersion,
                'counterVersion' => $submittedCounter,
                'itemsByAttribute' => $lastVersion->items->keyBy('attribute_id'),
                'counterItemsByAttribute' => $counterItemsByAttribute,
                'versions' => $versions,
                'isReadonly' => true,
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | CASE 3: NO COUNTER → CREATE NEW
    |--------------------------------------------------------------------------
    */

        $newVersion = $offer->versions()->create([
            'version_number' => $lastVersion->version_number + 1,
            'status' => 'draft',
            'is_counter' => 1,
            'created_by' => $context->user()->id,
            'comment' => null,
            'total_price' => $lastVersion->total_price,
        ]);

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

            if ($item->options->count()) {
                $newItem->options()->sync($item->options->pluck('id'));
            }
        }

        $versions = $offer->versions()
            ->with(['items.options'])
            ->orderByDesc('version_number')
            ->get();

        return view('rfq.workspace.create-counter-offer', [
            'rfq' => $rfq,
            'offer' => $offer,
            'offerVersion' => $lastVersion,
            'counterVersion' => $newVersion,
            'itemsByAttribute' => $newVersion->items->keyBy('attribute_id'),
            'counterItemsByAttribute' => $newVersion->items->load('options')->keyBy('attribute_id'),
            'versions' => $versions,
        ]);
    }


    public function autosave(
        Rfq $rfq,
        Request $request,
        OfferVersionItemAutosaveAction $action,
        ActiveContextService $context
    ) {
        abort_if(!$context->supplier(), 403);

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
}
