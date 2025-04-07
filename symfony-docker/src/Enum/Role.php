<?php
declare(strict_types=1);

namespace App\Enum;

enum Role: string
{
    case ADMIN = 'ROLE_ADMIN';
    case AGENT = 'ROLE_AGENT';
    case CUSTOMER = 'ROLE_CUSTOMER';
}
