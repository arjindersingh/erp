<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum ThemePresetStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';
}
