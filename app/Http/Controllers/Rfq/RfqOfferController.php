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
     * SUPPLIER: create offer (initial version)
     */
    public function store(
        Request $request,
        CreateRfqOfferAction $action,
        ActiveContextService $context
    ) {
       $supplier = $context->supplier();

        abort_if(!$supplier, 403);
        

        $offer = $action->execute(
            CreateRfqOfferData::fromArray($request->all()),
            $supplier->id,
        );

        return response()->json($offer);
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

    public function autosave(
    Rfq $rfq,
    Request $request,
    OfferVersionBuilder $builder,
    ActiveContextService $context
) {
    abort_if(!$context->supplier(), 403);

    $version = $builder->getDraftVersion(
        rfqId: $rfq->id,
        supplierId: $context->supplierId()
    );

    $payload = [];

    /*
    |--------------------------------------------------------------------------
    | NOTES
    |--------------------------------------------------------------------------
    */

    if ($request->has('notes')) {

        $payload['notes'] = $request->notes;
    }

    /*
    |--------------------------------------------------------------------------
    | PRICE
    |--------------------------------------------------------------------------
    */

    if ($request->has('unit_price')) {

        $payload['unit_price'] = $request->unit_price;
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT
    |--------------------------------------------------------------------------
    */

    if ($request->has('option_id')) {

        $payload['option_id'] = $request->option_id;
    }

    /*
    |--------------------------------------------------------------------------
    | MULTISELECT
    |--------------------------------------------------------------------------
    */

    if ($request->has('option_ids')) {

        $payload['option_ids'] = $request->option_ids;
    }

    logger()->info('AUTOSAVE', [
        'request' => $request->all(),
        'payload' => $payload,
    ]);

    if (empty($payload)) {

        return response()->json([
            'ok' => false,
            'debug' => 'payload empty'
        ]);
    }

    $builder->updateItem(
        version: $version,
        attributeId: $request->requirement_id,
        payload: $payload
    );

    return response()->json([
        'ok' => true
    ]);
}


public function buyerCounterAutosave(
    Rfq $rfq,
    RfqOffer $offer,
    RfqOfferVersion $version,
    Request $request,
    OfferVersionBuilder $builder,
    ActiveContextService $context
) {
    

    /*
    |----------------------------------------------------------------------
    | ACCESS CHECK
    |----------------------------------------------------------------------
    */

    

    abort_unless($version->rfq_offer_id === $offer->id, 403);
    abort_unless($version->status === 'draft', 403);
    abort_unless($version->is_counter, 403);

    logger()->info('AUTOSAVE', $request->all());

    /*
    |----------------------------------------------------------------------
    | BUILD PAYLOAD (FLAT REQUEST)
    |----------------------------------------------------------------------
    */

    $payload = [];

    if ($request->has('notes')) {
        $payload['notes'] = $request->input('notes');
    }

    if ($request->has('unit_price')) {
        $payload['unit_price'] = $request->input('unit_price');
    }

    if ($request->has('option_id')) {
        $payload['option_id'] = $request->input('option_id');
    }

    if ($request->has('option_ids')) {
        $payload['option_ids'] = $request->input('option_ids', []);
    }

    /*
    |----------------------------------------------------------------------
    | EMPTY CHECK
    |----------------------------------------------------------------------
    */

    if (empty($payload)) {
        return response()->json([
            'ok' => false,
            'message' => 'Payload empty',
        ]);
    }

    /*
    |----------------------------------------------------------------------
    | SAVE VIA BUILDER
    |----------------------------------------------------------------------
    */

    $builder->updateItem(
        version: $version,
        attributeId: (int) $request->input('attribute_id'),
        payload: $payload
    );

    return response()->json([
        'ok' => true,
    ]);
}



public function customAutosave(
    Rfq $rfq,
    Request $request,
    OfferVersionBuilder $builder,
    ActiveContextService $context
) {
    abort_if(!$context->supplier(), 403);

    $version = $builder->getDraftVersion(
        rfqId: $rfq->id,
        supplierId: $context->supplierId()
    );

    $requirementId = (int) $request->input('requirement_id');
    $field = $request->input('field');
    $value = $request->input('value');

    logger()->info('CUSTOM AUTOSAVE HIT', $request->all());

    if (!$requirementId || !$field) {
        return response()->json(['ok' => false]);
    }

    // ✅ ТОЛЬКО ЭТОТ МЕТОД
    $builder->updateCustomRequirement(
        version: $version,
        requirementId: $requirementId,
        key: $field,
        value: $value
    );

    return response()->json(['ok' => true]);
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
    ActiveContextService $context
) {
    /*
    |--------------------------------------------------------------------------
    | BUYER
    |--------------------------------------------------------------------------
    */

    $buyer = $context->isPersonal()
        ? auth()->user()
        : $context->company();

    if (!$buyer) {
        abort(403);
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD OFFER
    |--------------------------------------------------------------------------
    */

    $offer->loadMissing([
        'versions.items.options',
        'participant',
    ]);

    /*
    |--------------------------------------------------------------------------
    | LAST VERSION
    |--------------------------------------------------------------------------
    */

    $lastVersion = $offer->versions()
        ->with(['items.options'])
        ->where(['is_counter' => 0])
        ->orderByDesc('version_number')
        ->first();

    if (!$lastVersion) {
        abort(404, 'No version found');
    }

    /*
    |--------------------------------------------------------------------------
    | PREVENT DUPLICATE DRAFT COUNTER VERSION
    |--------------------------------------------------------------------------
    */

    $existingDraft = $offer->versions()
        ->where('is_counter', 1)
        ->where('status', 'draft')
        ->where('created_by', $context->user()->id)
        ->latest()
        ->first();

    if ($existingDraft) {

    $existingDraftCounter = $offer->versions()
                    ->where('is_counter', 1)
                    ->where('status', 'draft')
                    ->where('created_by', auth()->id())
                    ->orderByDesc('version_number')
                    ->first();

    $versions = $offer->versions()
    ->with(['items'])
    ->orderByDesc('version_number')
    ->get();

    $counterItemsByAttribute = $existingDraftCounter
                        ? $existingDraftCounter->items()
                        ->with('options')
                        ->get()
                        ->keyBy('attribute_id')
                        : collect();

    

        // ❗ ВАЖНО: просто показываем view, НЕ redirect
        return view('rfq.workspace.create-counter-offer', [
            'rfq' => $rfq,
            'offer' => $offer,
            'offerVersion' => $lastVersion,
            'counterVersion' => $existingDraft,
            'itemsByAttribute' => $lastVersion->items->keyBy('attribute_id'),
            'versions' => $versions, 
            'counterItemsByAttribute' => $counterItemsByAttribute ?? collect(),

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE COUNTER VERSION
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

    /*
    |--------------------------------------------------------------------------
    | CLONE ITEMS
    |--------------------------------------------------------------------------
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

        if ($item->options->count()) {
            $newItem->options()->sync($item->options->pluck('id'));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD ITEMS MAP
    |--------------------------------------------------------------------------
    */

    $itemsByAttribute = $newVersion
        ->items
        ->keyBy('attribute_id');

    $versions = $offer->versions()
    ->with(['items'])
    ->orderByDesc('version_number')
    ->get();

    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

    return view('rfq.workspace.create-counter-offer', [
        'rfq' => $rfq,
        'offer' => $offer,
        'offerVersion' => $lastVersion,
        'counterVersion' => $newVersion,
        'itemsByAttribute' => $itemsByAttribute,
        'versions' => $versions, 
    ]);
}

}