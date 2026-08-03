<?php

declare(strict_types=1);

namespace App\Core\Navigation;

enum LinkTarget: string
{
    case SameWindow = 'same_window';
    case NewTab = 'new_tab';
    case Modal = 'modal';
    case Drawer = 'drawer';
}
