<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum ContentWidth: string
{
    case Standard = 'standard';
    case Wide = 'wide';
    case Full = 'full';
}
