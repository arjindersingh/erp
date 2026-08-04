<?php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Authentication\ActiveContext;
use App\Models\User;
use Illuminate\Support\Collection;

final class EffectiveAccessService
{
    /** @return Collection<int, Permission> */
    public function permissions(User $user, ActiveContext $context): Collection
    {
        return Permission::query()->whereHas('roles.assignments', fn ($q) => $q
            ->where('user_id', $user->id)->where('user_membership_id', $context->membership->id)
            ->where('status', 'active')->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now())))
            ->whereHas('roles.permissions', fn ($q) => $q->where('role_permissions.status', 'active')
                ->where(fn ($q) => $q->whereNull('role_permissions.expires_at')->orWhere('role_permissions.expires_at', '>', now())))
            ->where('status', 'active')->get()->unique('id')->values();
    }

    public function allows(User $user, ActiveContext $context, string $permission): bool
    {
        return $this->permissions($user, $context)->contains('code', $permission);
    }
}
