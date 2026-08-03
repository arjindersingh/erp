<?php

declare(strict_types=1);

namespace App\Core\Navigation\Exceptions;

use InvalidArgumentException;

final class InvalidMenuItem extends InvalidArgumentException
{
    public static function because(string $reason): self
    {
        return new self('Invalid menu item: '.$reason);
    }
}
