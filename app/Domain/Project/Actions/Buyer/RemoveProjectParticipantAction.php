<?php

namespace App\Domain\Project\Actions\Buyer;

use App\Domain\Project\Models\ProjectParticipant;
use App\Domain\Project\Services\ProjectParticipantService;

class RemoveProjectParticipantAction
{
    public function __construct(
        private ProjectParticipantService $participants
    ) {}

    public function execute(ProjectParticipant $participant): void
    {
        $this->participants->remove($participant);
    }
}