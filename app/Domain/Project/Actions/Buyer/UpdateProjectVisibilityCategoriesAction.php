<?php

namespace App\Domain\Project\Actions\Buyer;

use App\Domain\Project\Models\Project;

class UpdateProjectVisibilityCategoriesAction
{
    public function execute(
        Project $project,
        array $categoryIds
    ): void {

        $project->visibilityCategories()->sync($categoryIds);
    }
}