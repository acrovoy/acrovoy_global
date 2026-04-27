<?php

namespace App\Domain\Negotiation\Enums;

enum OfferVersionStatusEnum:string
{
    case DRAFT = 'draft';

    case SENT = 'sent';

    case COUNTERED = 'countered';

    case ACCEPTED = 'accepted';

    case REJECTED = 'rejected';
}