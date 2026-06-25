<?php

namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Enums\RfqStatus;
use App\Services\Company\ActiveContextService;

class ListBuyerRfqsAction
{
    public function execute(ActiveContextService $context)
{
    $query = Rfq::query()
        ->with(['category']);

    if ($context->isPersonal()) {
        $query->where('buyer_type', auth()->user()::class)
              ->where('buyer_id', auth()->id());
    } else {
        $query->where('buyer_type', $context->type())
              ->where('buyer_id', $context->id());
    }

    $all = $query->latest()->get();

    return [
    'active' => (clone $query)
        ->where('status', '!=', RfqStatus::CLOSED)
        ->latest()
        ->paginate(10, ['*'], 'active_page'),

    'closed' => (clone $query)
        ->where('status', RfqStatus::CLOSED)
        ->latest()
        ->paginate(10, ['*'], 'closed_page'),
];
}
}