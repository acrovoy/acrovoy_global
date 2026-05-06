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
        $supplier = $context->company();

        abort_if(!$supplier, 403);
        abort_if($context->role() !== 'supplier', 403);

        $offer = $action->execute(
            CreateRfqOfferData::fromArray($request->all()),
            $supplier->id
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
        abort_if($context->role() !== 'buyer', 403);

        $service->accept(
            $version,
            $context->user()->id
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

    $field = $request->input('field');
    $value = $request->input('value');

    /**
     * =========================
     * PRICE DETECTION (ROBUST)
     * =========================
     */
    if (
        str_contains($field, 'price') ||
        $request->has('unit_price')
    ) {
        $payload['unit_price'] = is_numeric($value)
            ? $value
            : $request->input('unit_price');
    }

    /**
     * =========================
     * NOTES DETECTION
     * =========================
     */
    if (
        str_contains($field, 'notes') ||
        $request->has('notes')
    ) {
        $payload['notes'] = $value;
    }

    /**
     * =========================
     * SELECT
     * =========================
     */
    if ($request->filled('option_id')) {
        $payload['option_id'] = $request->input('option_id');
    }

    /**
     * =========================
     * MULTISELECT
     * =========================
     */
    if ($request->filled('option_ids')) {
        $payload['option_ids'] = $request->input('option_ids');
    }

    /**
     * DEBUG (ВАЖНО)
     */
    logger()->info('AUTOSAVE', [
        'request' => $request->all(),
        'payload' => $payload,
    ]);

    if (empty($payload)) {
        return response()->json([
            'ok' => false,
            'debug' => 'payload empty',
            'request' => $request->all(),
        ]);
    }

    $builder->updateItem(
        version: $version,
        attributeId: $request->input('requirement_id'),
        payload: $payload
    );

    return response()->json(['ok' => true]);
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


}