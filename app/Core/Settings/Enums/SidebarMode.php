<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum SidebarMode: string
{
    case Expanded = 'expanded';
    case Collapsed = 'collapsed';
    case Auto = 'auto';
}
