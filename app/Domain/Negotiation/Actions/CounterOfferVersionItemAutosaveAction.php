<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Services\OfferVersionBuilder;
use App\Domain\Negotiation\Models\RfqOfferVersion;
use Illuminate\Http\Request;
use App\Services\Company\ActiveContextService;
use Illuminate\Support\Facades\Log;

class CounterOfferVersionItemAutosaveAction
{
    public function __construct(
        private OfferVersionBuilder $builder
    ) {}

    public function execute(
        RfqOfferVersion $version,
        Request $request,
        ActiveContextService $context
    ) {
        Log::info('BUYER COUNTER AUTOSAVE', $request->all());

        /**
         * =========================
         * UPDATE TOTAL PRICE
         * =========================
         */
        if ($request->has('total_price')) {

            $version->update([
                'total_price' => $request->input('total_price') !== ''
                    ? $request->input('total_price')
                    : null,
            ]);

            return response()->json([
                'ok' => true,
            ]);
        }


        $payload = $this->buildPayload($request);

        if (empty($payload)) {
            return response()->json([
                'ok' => false,
                'message' => 'Payload empty',
            ]);
        }


        $this->builder->updateItem(
            version: $version,
            attributeId: (int) $request->input('attribute_id'),
            payload: $payload,
            actor: $context->user()
        );

        return response()->json([
            'ok' => true,
        ]);
    }

    private function buildPayload(Request $request): array
    {
        return array_filter([
            'notes' => $request->input('notes'),
            'unit_price' => $request->input('unit_price'),
            'option_id' => $request->input('option_id'),
            'option_ids' => $request->input('option_ids', []),
        ], fn($v) => $v !== null);
    }
}
