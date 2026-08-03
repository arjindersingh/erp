<?php

declare(strict_types=1);

namespace App\Core\Identity;

enum AccountType: string
{
    case Person = 'person';
    case SiteAdmin = 'site_admin';
    case Service = 'service';
    case Api = 'api';
    case System = 'system';
}
