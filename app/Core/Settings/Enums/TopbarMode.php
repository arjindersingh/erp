<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum TopbarMode: string
{
    case Visible = 'visible';
    case Hidden = 'hidden';
    case Sticky = 'sticky';
    case Static = 'static';
}
