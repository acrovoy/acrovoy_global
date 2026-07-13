<?php

namespace App\Domain\Project\Services;

use App\Domain\Project\Models\Project;
use App\Services\Company\ActiveContextService;
use App\Domain\Project\Enums\ProjectStatus;
use App\Domain\Project\Enums\ProjectVisibilityType;
use App\Models\Supplier;

class ProjectVisibilityService
{
    /**
     * Can current context view this project?
     */
    public function canViewInList(
        Project $project,
        ActiveContextService $context
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | OWNER
        |--------------------------------------------------------------------------
        */

        if (
            $project->buyer_id === $context->id()
            && $project->buyer_type === $context->type()
        ) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | ONLY SUPPLIERS дальше
        |--------------------------------------------------------------------------
        */

        if (!$context->isCompany()) {
            return false;
        }

        if ($context->type() !== Supplier::class) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | INVITED PARTICIPANT
        |--------------------------------------------------------------------------
        */

        $isParticipant = $project->participants()
            ->where('participant_type', Supplier::class)
            ->where('participant_id', $context->id())
            ->exists();

        if ($isParticipant) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | ONLY PUBLISHED дальше
        |--------------------------------------------------------------------------
        */

        if ($project->status !== ProjectStatus::PUBLISHED) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY DISCOVERY
        |--------------------------------------------------------------------------
        */

        if ($project->visibility_type === ProjectVisibilityType::CATEGORY) {
            return $this->supplierMatchesCategory($project, $context);
        }

        /*
        |--------------------------------------------------------------------------
        | PLATFORM
        |--------------------------------------------------------------------------
        */

        if ($project->visibility_type === ProjectVisibilityType::PLATFORM) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | OPEN
        |--------------------------------------------------------------------------
        */

        if ($project->visibility_type === ProjectVisibilityType::OPEN) {
            return true;
        }

        return false;
    }

    /**
     * Filter visible projects
     */
    public function filterVisible(
        $query,
        ActiveContextService $context
    ) {
        return $query->where(function ($q) use ($context) {

            /*
            |--------------------------------------------------------------------------
            | OWNER
            |--------------------------------------------------------------------------
            */

            if ($context->isCompany()) {

                $q->orWhere(function ($owner) use ($context) {

                    $owner
                        ->where('buyer_id', $context->id())
                        ->where('buyer_type', $context->type());

                });
            }

            /*
            |--------------------------------------------------------------------------
            | SUPPLIER ACCESS
            |--------------------------------------------------------------------------
            */

            if (
                $context->isCompany()
                && $context->type() === Supplier::class
            ) {

                $supplierId = $context->id();

                /*
                |--------------------------------------------------------------------------
                | PARTICIPANT
                |--------------------------------------------------------------------------
                */

                $q->orWhereHas('participants', function ($participant) use ($supplierId) {

                    $participant
                        ->where('participant_type', Supplier::class)
                        ->where('participant_id', $supplierId);

                });

                /*
                |--------------------------------------------------------------------------
                | CATEGORY
                |--------------------------------------------------------------------------
                */

                $q->orWhere(function ($category) {

                    $category
                        ->where('visibility_type', ProjectVisibilityType::CATEGORY)
                        ->where('status', ProjectStatus::PUBLISHED);

                });

                /*
                |--------------------------------------------------------------------------
                | PLATFORM
                |--------------------------------------------------------------------------
                */

                $q->orWhere(function ($platform) {

                    $platform
                        ->where('visibility_type', ProjectVisibilityType::PLATFORM)
                        ->where('status', ProjectStatus::PUBLISHED);

                });

                /*
                |--------------------------------------------------------------------------
                | OPEN
                |--------------------------------------------------------------------------
                */

                $q->orWhere(function ($open) {

                    $open
                        ->where('visibility_type', ProjectVisibilityType::OPEN)
                        ->where('status', ProjectStatus::PUBLISHED);

                });
            }
        });
    }

    /**
     * Category matching
     */
    private function supplierMatchesCategory(
        Project $project,
        ActiveContextService $context
    ): bool {

        /*
        Здесь позже появится логика:

        supplier_categories
        project_visibility_categories

        Пока разрешаем.
        */

        return true;
    }
}
