<?php

declare(strict_types=1);

namespace App\Core\Authorization\Exceptions;

use DomainException;

class InvalidRoleAssignment extends DomainException
{
    public static function because(string $reason): self
    {
        return new self('Invalid role assignment: '.$reason);
    }
}
