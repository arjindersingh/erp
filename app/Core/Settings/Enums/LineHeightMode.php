<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum LineHeightMode: string
{
    case Compact = 'compact';
    case Normal = 'normal';
    case Relaxed = 'relaxed';
}
