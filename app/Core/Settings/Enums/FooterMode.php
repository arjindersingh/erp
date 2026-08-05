<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum FooterMode: string
{
    case Standard = 'standard';
    case Compact = 'compact';
    case Minimal = 'minimal';
    case Hidden = 'hidden';
}
