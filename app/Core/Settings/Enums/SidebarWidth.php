<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum SidebarWidth: string
{
    case Compact = 'compact';
    case Standard = 'standard';
    case Wide = 'wide';
    case Custom = 'custom';
}
