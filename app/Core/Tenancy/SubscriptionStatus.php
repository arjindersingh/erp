<?php

declare(strict_types=1);

namespace App\Core\Tenancy;

enum SubscriptionStatus: string
{
    case Trial = 'trial';
    case Active = 'active';
    case PastDue = 'past_due';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function allowsService(): bool
    {
        return in_array($this, [self::Trial, self::Active], true);
    }
}
