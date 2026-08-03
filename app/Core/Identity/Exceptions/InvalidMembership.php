<?php

declare(strict_types=1);

namespace App\Core\Identity\Exceptions;

use DomainException;

class InvalidMembership extends DomainException
{
    public static function because(string $reason): self
    {
        return new self('Invalid membership: '.$reason);
    }
}
