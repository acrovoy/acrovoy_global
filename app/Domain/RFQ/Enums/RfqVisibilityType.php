<?php


namespace App\Domain\RFQ\Enums;

enum RfqVisibilityType: string
{
    case PRIVATE = 'private';
    case CATEGORY = 'category';
    case PLATFORM = 'platform';
    case OPEN = 'open';
}