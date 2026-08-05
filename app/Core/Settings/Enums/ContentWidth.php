<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum ContentWidth: string
{
    case Full = 'full';
    case Wide = 'wide';
    case Standard = 'standard';
    case Narrow = 'narrow';
    case Boxed = 'boxed';
}
