<?php

namespace App\Core\Tenancy\Exceptions;

use RuntimeException;

class TenantCouldNotBeResolved extends RuntimeException
{
    public static function forHost(string $host): self
    {
        return new self("No tenant could be resolved for host [{$host}].");
    }
}
