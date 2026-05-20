<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Services\OfferVersionBuilder;
use App\Domain\RFQ\Models\Rfq;
use Illuminate\Http\Request;
use App\Services\Company\ActiveContextService;


class OfferVersionItemAutosaveAction
{
    public function __construct(
        private OfferVersionBuilder $builder
    ) {}

    public function execute(
        Rfq $rfq,
        Request $request,
        ActiveContextService $context
    ) {
        $version = $this->builder->getDraftVersion(
            rfqId: $rfq->id,
            supplierId: $context->supplierId()
        );

        $payload = $this->buildPayload($request);

        if (empty($payload)) {
            return response()->json([
                'ok' => false,
                'debug' => 'payload empty'
            ]);
        }

        $this->builder->updateItem(
            version: $version,
            attributeId: $request->requirement_id,
            payload: $payload,
            actor: $context->user()
        );

        return response()->json(['ok' => true]);
    }

    private function buildPayload(Request $request): array
    {
        return array_filter([
            'notes' => $request->notes,
            'unit_price' => $request->unit_price,
            'option_id' => $request->option_id,
            'option_ids' => $request->option_ids,
        ], fn ($v) => $v !== null);
    }
}