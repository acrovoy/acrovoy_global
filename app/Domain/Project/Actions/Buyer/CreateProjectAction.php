<?php

namespace App\Domain\Project\Actions\Buyer;

use App\Domain\Project\DTO\CreateProjectData;
use App\Domain\Project\Enums\ProjectStatus;

use App\Domain\Project\Models\Project;
use Illuminate\Support\Str;

class CreateProjectAction
{
    public function execute(CreateProjectData $data, $buyerId, $buyerType, int $createdBy): Project
{


    return Project::create([
        'buyer_type' => $buyerType,
        'buyer_id'   => $buyerId,

        'created_by' => $createdBy,
        'title' => $data->title,
        'description' => $data->description,
        'status' => ProjectStatus::DRAFT,
        'closed_at' => $data->closed_at,
    ]);
}
}