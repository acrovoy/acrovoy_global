<?php

namespace App\Domain\Project\Services;

use App\Domain\Project\Models\Project;
use App\Domain\Project\Models\ProjectParticipant;
use App\Domain\Project\Enums\ProjectParticipantStatus;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Supplier;

class ProjectParticipantService
{
    /**
     * Invite any participant (polymorphic)
     */
    public function invite(Project $project, Model $participant): ProjectParticipant
    {
        return ProjectParticipant::updateOrCreate(
            [
                'project_id'       => $project->id,
                'participant_type' => $participant::class,
                'participant_id'   => $participant->id,
            ],
            [
                'status'      => ProjectParticipantStatus::INVITED,
                'invited_at'  => Carbon::now(),
            ]
        );
    }

    /**
     * Invite suppliers by ids
     */
    public function inviteSuppliers(Project $project, iterable $supplierIds): void
    {
        $suppliers = Supplier::whereIn('id', $supplierIds)->get();

        foreach ($suppliers as $supplier) {
            $this->invite($project, $supplier);
        }
    }

    /**
     * External email invitation
     */
    public function inviteByEmail(Project $project, string $email): void
    {
        \Mail::raw(
            "You were invited to Project #{$project->id}",
            fn ($mail) => $mail
                ->to($email)
                ->subject('Project Invitation')
        );
    }

    /**
     * Soft remove participant
     */
    public function remove(ProjectParticipant $participant): void
    {
        if ($participant->status === ProjectParticipantStatus::REMOVED) {
            return;
        }

        $participant->update([
            'status' => ProjectParticipantStatus::REMOVED,
        ]);
    }
}