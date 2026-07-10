<?php

namespace App\Domain\Project\Actions\Buyer;

use App\Domain\Project\Models\Project;
use App\Domain\Project\Enums\ProjectStatus;
use App\Services\Company\ActiveContextService;

class ListBuyerProjectAction
{
    public function execute(ActiveContextService $context)
    {
        $query = Project::query()
            ->where('buyer_type', $context->type())
            ->where('buyer_id', $context->id());

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
