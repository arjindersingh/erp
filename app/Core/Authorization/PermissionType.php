<?php

declare(strict_types=1);

namespace App\Core\Authorization;

enum PermissionType: string
{
    case Module = 'module';
    case Resource = 'resource';
    case Command = 'command';
    case Approval = 'approval';
    case Report = 'report';
    case Field = 'field';
    case Special = 'special';
}
