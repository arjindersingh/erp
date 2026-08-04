<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum PreferenceChangeSource: string
{
    case User = 'user';
    case TenantAdmin = 'tenant_admin';
    case Support = 'support';
    case System = 'system';
    case Migration = 'migration';
    case Reset = 'reset';
}
