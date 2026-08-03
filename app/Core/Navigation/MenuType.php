<?php

declare(strict_types=1);

namespace App\Core\Navigation;

enum MenuType: string
{
    case Sidebar = 'sidebar';
    case Topbar = 'topbar';
    case Mobile = 'mobile';
    case QuickActions = 'quick_actions';
    case Footer = 'footer';
    case PublicHeader = 'public_header';
    case PublicFooter = 'public_footer';
}
