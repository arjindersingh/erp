<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum CardRadius: string
{
    case Square = 'square';
    case Soft = 'soft';
    case Rounded = 'rounded';
}
