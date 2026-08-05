<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum AnimationLevel: string
{
    case None = 'none';
    case Reduced = 'reduced';
    case Standard = 'standard';
    case Enhanced = 'enhanced';
}
