<?php

declare(strict_types=1);

namespace App\Core\Modules;

use App\Core\Authentication\ActiveContext;
use App\Core\Authorization\EffectiveAccessService;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

final readonly class ModuleNavigationResolver
{
    public function __construct(private EffectiveAccessService $access) {}

    /** @return Collection<int, Module> */
    public function forContext(User $user, Tenant $tenant, ActiveContext $context): Collection
    {
        $permissionCodes = $this->access->permissions($user, $context)->pluck('code');
        $now = now();

        return Module::query()
            ->where('status', 'active')
            ->whereNotNull('default_route_name')
            ->whereHas('permissions', fn (Builder $query) => $query->whereIn('code', $permissionCodes))
            ->where(function (Builder $query) use ($tenant, $now): void {
                $query->where('is_core', true)
                    ->orWhereHas('tenantConfigurations', function (Builder $query) use ($tenant, $now): void {
                        $query->withoutGlobalScopes()
                            ->where('tenant_id', $tenant->id)
                            ->where('is_enabled', true)
                            ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
                            ->where(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', $now));
                    });
            })
            ->orderBy('display_order')
            ->get()
            ->filter(function (Module $module) use ($permissionCodes): bool {
                $routeName = $module->default_route_name;

                return $routeName !== null
                    && Route::has($routeName)
                    && $permissionCodes->contains($module->code.'.dashboard.view');
            })
            ->values();
    }
}
