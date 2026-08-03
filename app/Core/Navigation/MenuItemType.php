<?php

declare(strict_types=1);

namespace App\Core\Navigation;

enum MenuItemType: string
{
    case Group = 'group';
    case Link = 'link';
    case Action = 'action';
    case Dashboard = 'dashboard';
    case Report = 'report';
    case Separator = 'separator';
    case Heading = 'heading';
    case ExternalLink = 'external_link';
    case ContextSwitcher = 'context_switcher';
    case Search = 'search';

    public function mayContainChildren(): bool
    {
        return $this === self::Group;
    }
}
