<?php

declare(strict_types=1);

namespace App\Core\Tenancy;

enum DomainType: string
{
    case Subdomain = 'subdomain';
    case Custom = 'custom';
}
