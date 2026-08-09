<?php

namespace App\Domain\Project\Actions\Buyer;

use App\Domain\Project\Models\Project;
use App\Domain\Project\Enums\ProjectStatus;
use App\Services\Company\ActiveContextService;

class ListBuyerProjectAction
{
    public function execute(ActiveContextService $context)
    {
        $buyer = $context->buyerProfile();

        abort_unless($buyer, 403);

        $query = Project::query()
            ->where('buyer_type', $buyer::class)
            ->where('buyer_id', $buyer->getKey());

        return [
            'active' => (clone $query)
                ->where('status', '!=', ProjectStatus::CLOSED)
                ->latest()
                ->paginate(10, ['*'], 'active_page'),

            'closed' => (clone $query)
                ->where('status', ProjectStatus::CLOSED)
                ->latest()
                ->paginate(10, ['*'], 'closed_page'),
        ];
    }
}