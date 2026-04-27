<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Services\RfqAccessService;
use App\Services\Company\ActiveContextService;

class SupplierRfqController extends Controller
{
    /**
     * RFQ LIST (incoming RFQs for supplier)
     */
    public function index(
    ActiveContextService $context,
    RfqAccessService $access
)
{
    $rfqs = $access->getAvailableRfqsForSupplier(
        [
            'mode' => $context->mode(),
            'company_id' => $context->id(),
            'company_type' => $context->type(),
            'role' => $context->role(),
        ],
        auth()->id()
    );

    return view('rfq.supplier.index', compact('rfqs'));
}

    /**
     * RFQ WORKSPACE (supplier side)
     */
    public function show(
    Rfq $rfq,
    RfqAccessService $access,
    ActiveContextService $context
)
{
    abort_unless(
        $access->canViewRfq(
            $rfq,
            [
                'mode' => $context->mode(),
                'company_id' => $context->id(),
                'company_type' => $context->type(),
                'role' => $context->role(),
            ],
            auth()->id()
        ),
        403
    );

    return view('rfq.supplier.show', compact('rfq'));
}
}