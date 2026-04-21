<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Dev = 'ROLE_DEV';
    case User = 'ROLE_USER';
}
