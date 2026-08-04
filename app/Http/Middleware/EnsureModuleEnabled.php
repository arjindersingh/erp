<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Modules\TenantModule;
use App\Core\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string $moduleCode): Response
    {
        $tenant = app(TenantContext::class)->tenant();
        abort_if($tenant === null, 404);
        $enabled = TenantModule::withoutGlobalScopes()->where('tenant_id', $tenant->id)
            ->whereHas('module', fn ($q) => $q->where('code', $moduleCode)->where('status', 'active'))
            ->get()->contains(fn (TenantModule $module) => $module->isEffectiveAt());
        abort_unless($enabled, $request->user() === null ? 404 : 403);

        return $next($request);
    }
}
