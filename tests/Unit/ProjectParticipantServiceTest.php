<?php

namespace Tests\Unit;

use App\Domain\Email\Services\EmailService;
use App\Domain\Project\Models\Project;
use App\Domain\Project\Services\ProjectParticipantService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ProjectParticipantServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_sends_project_invitation_through_email_service(): void
    {
        $emailService = Mockery::mock(EmailService::class);

        $emailService
            ->shouldReceive('sendSystemEmail')
            ->once()
            ->with(
    'supplier@example.com',
    'Project Invitation',
    'You were invited to Project #456',
);

        $project = new Project();
        $project->setAttribute('id', 456);

        $service = new ProjectParticipantService($emailService);

        $service->inviteByEmail($project, 'supplier@example.com');
    }
}
