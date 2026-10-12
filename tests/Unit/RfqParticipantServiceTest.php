<?php

namespace Tests\Unit;

use App\Domain\Email\Services\EmailService;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Services\RfqParticipantService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class RfqParticipantServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_sends_rfq_invitation_through_email_service(): void
    {
        $emailService = Mockery::mock(EmailService::class);

        $emailService
            ->shouldReceive('sendSystemEmail')
            ->once()
            ->with(
    'supplier@example.com',
    'RFQ Invitation',
    'You were invited to RFQ #123',
);

        $rfq = new Rfq();
        $rfq->setAttribute('id', 123);

        $service = new RfqParticipantService($emailService);

        $service->inviteByEmail($rfq, 'supplier@example.com');
    }
}
