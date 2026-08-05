<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum SidebarState: string
{
    case Expanded = 'expanded';
    case Collapsed = 'collapsed';
    case Hidden = 'hidden';
    case Auto = 'auto';
    case Overlay = 'overlay';
}
