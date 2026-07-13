<?php

namespace App\Domain\Project\Actions\Buyer;

use App\Domain\Project\Models\Project;
use App\Domain\Project\Services\ProjectParticipantService;
use Illuminate\Database\Eloquent\Model;

class InviteProjectParticipantAction
{
    public function __construct(
        private ProjectParticipantService $participants
    ) {}

    public function execute(
        Project $project,
        Model $participant
    )
    {
        return $this->participants->invite(
            $project,
            $participant
        );
    }
}