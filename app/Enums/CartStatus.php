<?php

namespace App\Enums;

enum CartStatus: string
{
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case CONVERTED = 'converted';
    case ABANDONED = 'abandoned';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
}

