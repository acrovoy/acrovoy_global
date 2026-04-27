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
}