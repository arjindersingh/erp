<?php

declare(strict_types=1);

namespace App\Core\Authorization\Exceptions;

use InvalidArgumentException;

final class InvalidPermissionCode extends InvalidArgumentException
{
    public static function for(string $code): self
    {
        return new self("Invalid permission code [{$code}]. Expected module.resource.command or module.access.");
    }
}
