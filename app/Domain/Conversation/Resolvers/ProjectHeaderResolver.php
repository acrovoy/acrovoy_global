<?php

namespace App\Domain\Conversation\Resolvers;

use App\Domain\Conversation\Contracts\ConversationHeaderResolver;
use App\Domain\Conversation\Models\Conversation;
use App\Domain\Project\Models\Project;

class ProjectHeaderResolver implements ConversationHeaderResolver
{
    public function supports(string $subjectType): bool
    {
        return $subjectType === Project::class;
    }

    public function resolve(Conversation $conversation): array
    {
        return [

            'title' => 'Project',

            'subtitle' => null,

            'avatar' => null,

            'manager' => [

                'id' => null,

                'name' => null,

                'avatar' => null,

                'position' => null,

            ],

            'company' => [

                'id' => null,

                'name' => null,

                'logo' => null,

            ],

            'online' => false,

            'last_seen' => null,

        ];
    }
}