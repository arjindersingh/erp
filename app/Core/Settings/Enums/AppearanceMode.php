<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum AppearanceMode: string
{
    case System = 'system';
    case Light = 'light';
    case Dark = 'dark';
}
