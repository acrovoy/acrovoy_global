<?php

namespace App\Domain\Project\Actions\Supplier;

use App\Domain\Project\Models\Project;
use App\Domain\Project\Enums\ProjectStatus;
use App\Services\Company\ActiveContextService;

use App\Domain\Project\Enums\ProjectParticipantStatus;

class ListSupplierProjectsAction
{
    public function execute(ActiveContextService $context): array
    {
        $query = Project::query()

            ->whereHas('participants', function ($q) use ($context) {

                $q->where('participant_type', $context->type())
                    ->where('participant_id', $context->id())
                    ->where('status', '!=', ProjectParticipantStatus::REMOVED);

            })

            ->with([
                'participants' => function ($q) use ($context) {

                    $q->where('participant_type', $context->type())
                        ->where('participant_id', $context->id());

                },
            ]);

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