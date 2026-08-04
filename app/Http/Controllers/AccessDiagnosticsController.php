<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Authentication\ActiveContext;
use App\Core\Authentication\AuthenticatedProfileResolver;
use App\Core\Authorization\EffectiveAccessService;
use App\Core\Modules\TenantModule;
use App\Core\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccessDiagnosticsController
{
    public function __invoke(Request $request, ActiveContext $context, AuthenticatedProfileResolver $resolver, EffectiveAccessService $access): View
    {
        $tenant = app(TenantContext::class)->requireTenant();
        $profiles = $resolver->resolveFor($request->user(), $tenant);
        $permissions = $access->permissions($request->user(), $context);
        $roles = $context->membership->roleAssignments()->with('role')->active()->validAt()->get()->pluck('role.name');
        $modules = TenantModule::query()->with('module')->where('tenant_id', $tenant->id)->where('is_enabled', true)->get()->pluck('module.name');

        return view('admin.access-diagnostics', compact('tenant', 'context', 'profiles', 'permissions', 'roles', 'modules'));
    }
}
