<?php

namespace App\Domain\RFQ\Enums;

enum RfqType: string
{
    case PRODUCT = 'product';
    case SERVICE = 'service';
    case PROJECT = 'project';
}