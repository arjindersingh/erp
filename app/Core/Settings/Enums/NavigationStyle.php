<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum NavigationStyle: string
{
    case Sidebar = 'sidebar';
    case Topbar = 'topbar';
    case Hybrid = 'hybrid';
}
