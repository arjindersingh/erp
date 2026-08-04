<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Authentication\ActiveContext;
use App\Core\Identity\MembershipStatus;
use App\Core\Navigation\Portal;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

final class PanelAccessService
{
    public function canAccess(User $user, string $panel): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        $key = sprintf('panel-access:%s:%s', $user->getKey(), $panel);

        return Cache::remember($key, 60, fn () => $this->resolve($user, $panel));
    }

    private function resolve(User $user, string $panel): bool
    {
        $portal = Portal::query()->where('code', $panel)->where('status', 'active')->first();
        if ($portal === null) {
            return false;
        }

        $membership = $user->activeMemberships()
            ->where('metadata->portal_codes', 'like', '%'.$panel.'%')
            ->where('status', MembershipStatus::Active->value)
            ->first();

        if ($membership === null) {
            return false;
        }

        return $membership->accessScope()->exists() && $membership->accessScope->isActive();
    }
}
