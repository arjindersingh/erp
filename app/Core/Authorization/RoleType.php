<?php

declare(strict_types=1);

namespace App\Core\Authorization;

enum RoleType: string
{
    case Platform = 'platform';
    case Tenant = 'tenant';
    case Company = 'company';
    case Campus = 'campus';
    case Institute = 'institute';
    case Staff = 'staff';
    case Student = 'student';
    case Guardian = 'guardian';
    case Alumni = 'alumni';
    case Service = 'service';
}
