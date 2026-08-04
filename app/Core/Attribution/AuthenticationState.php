<?php

declare(strict_types=1);

namespace App\Core\Attribution;

enum AuthenticationState: string
{
    case Authenticated = 'authenticated';
    case UnauthenticatedPublic = 'unauthenticated_public';
    case PublicVerified = 'public_verified';
    case ServiceAuthenticated = 'service_authenticated';
    case SystemInternal = 'system_internal';
    case UnknownLegacy = 'unknown_legacy';
}
