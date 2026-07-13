<?php

namespace App\Domain\Project\Actions\Buyer;

use App\Domain\Project\Models\Project;
use App\Domain\Project\Enums\ProjectVisibilityType;

class UpdateProjectVisibilityAction
{
    public function execute(
        Project $project,
        ProjectVisibilityType $visibility
    ): Project {

        $project->update([
            'visibility_type' => $visibility,
        ]);

        return $project;
    }
}