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
    
        $query->where('buyer_type', $context->type())
              ->where('buyer_id', $context->id())
              ->whereNull('project_id');
    
    
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