<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Services\RfqAccessService;
use App\Services\Company\ActiveContextService;
use App\Domain\Negotiation\Models\RfqOffer;
use App\Models\ShippingDimensions;
use Illuminate\Http\Request;

class SupplierRfqController extends Controller
{
    /**
     * RFQ LIST (incoming RFQs for supplier)
     */
    public function index(
    RfqAccessService $access
) {
    $rfqs = $access->getAvailableRfqsForSupplier();

     $closedRfqs = $access->getClosedRfqsForSupplier();
     $closedRfqs->loadMissing(['offers']);

    return view('rfq.supplier.index', compact('rfqs',
        'closedRfqs'));
}

    /**
     * RFQ WORKSPACE (supplier side)
     */
     public function show(
    Rfq $rfq,
    RfqAccessService $access,
    ActiveContextService $context
) {
    abort_unless(
        $access->canViewRfq($rfq, auth()->id()),
        403
    );

    /*
    |----------------------------------------------------
    | LOAD EVERYTHING REQUIRED (NO N+1)
    |----------------------------------------------------
    */
    $rfq->loadMissing([
        'attributeValues.attribute.options.translations',
        'attributeValues.options.translations',
        'customAttributes',
    ]);

    /*
    |----------------------------------------------------
    | OFFER
    |----------------------------------------------------
    */
    $offer = RfqOffer::query()
        ->where('rfq_id', $rfq->id)
        ->where('participant_id', $context->supplierId())
        ->first();

    /*
    |----------------------------------------------------
    | SYSTEM REQUIREMENTS
    |----------------------------------------------------
    */
    $systemAttributes = $rfq->attributeValues->map(function ($req) {

        return (object) [
            'id' => $req->id,

            'name' => $req->attribute->name,
            'type' => $req->attribute->type,

            'value_text' => $req->value_text,
            'value_number' => $req->value_number,

            // SAFE OPTIONS (always collection)
            'options' => $req->attribute->options ?? collect(),

            // SELECT
            'selected_option_id' => $req->attribute_option_id,

            // MULTISELECT (safe fallback)
            'selected_options' => $req->options
                ? $req->options->pluck('id')->values()->toArray()
                : [],

            'is_custom' => false,
        ];
    });

    /*
    |----------------------------------------------------
    | CUSTOM REQUIREMENTS
    |----------------------------------------------------
    */
    $customAttributes = $rfq->customAttributes->map(function ($item) {

        return (object) [
            'id' => 'custom_' . $item->id,

            'name' => $item->key,
            'type' => 'custom',

            'value_text' => $item->value,

            'value_number' => null,

            'options' => collect(),

            'selected_option_id' => null,
            'selected_options' => [],

            'is_custom' => true,
        ];
    });

    /*
    |----------------------------------------------------
    | MERGE ALL (IMPORTANT)
    |----------------------------------------------------
    */
    $attributes = $systemAttributes->merge($customAttributes)->values();

    return view('rfq.supplier.opportunity', compact(
        'rfq',
        'offer',
        'attributes'
    ));
}


public function updateShippingDimensions(Request $request, Rfq $rfq,
    ActiveContextService $activeContext)
{
    
    $validated = $request->validate([
        'shipping.length' => ['required', 'numeric', 'min:0'],
        'shipping.width' => ['required', 'numeric', 'min:0'],
        'shipping.height' => ['required', 'numeric', 'min:0'],
        'shipping.weight' => ['required', 'numeric', 'min:0'],
        'shipping.package_type' => ['required', 'in:box,pallet,set'],
    ]);

    $shipping = $validated['shipping'];

    $rfq->shippingDimensions()->updateOrCreate(
    [
        'dimensionable_type' => $rfq->getMorphClass(),
        'dimensionable_id'   => $rfq->id,
        'supplier_type'      => $activeContext->type(),
        'supplier_id'        => $activeContext->id(),
    ],
    [
        'length'       => $shipping['length'],
        'width'        => $shipping['width'],
        'height'       => $shipping['height'],
        'weight'       => $shipping['weight'],
        'package_type' => $shipping['package_type'],
    ]
);

    

    return back()->with('success', 'Shipping dimensions have been saved.');
}


}