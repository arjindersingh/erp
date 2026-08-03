<?php

declare(strict_types=1);

namespace App\Core\Authorization\Exceptions;

use DomainException;

class InvalidAccessScope extends DomainException
{
    public static function because(string $reason): self
    {
        return new self('Invalid access scope: '.$reason);
    }
}
