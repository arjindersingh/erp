<?php

declare(strict_types=1);

namespace App\Core\Authorization;

enum ActiveContextStatus: string
{
    case Active = 'active';
    case Revoked = 'revoked';
    case Expired = 'expired';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
