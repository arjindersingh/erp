<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Authentication\ActiveContext;
use App\Core\Authentication\AuthenticatedProfileResolver;
use App\Core\Tenancy\TenantContext;
use App\Domains\Admissions\Models\AdmissionCampaign;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AdminDashboardController
{
    public function __invoke(Request $request, TenantContext $tenantContext, ActiveContext $context, AuthenticatedProfileResolver $resolver): View
    {
        $tenant = $tenantContext->requireTenant();
        $profiles = $resolver->resolveFor($request->user(), $tenant);
        $campaigns = AdmissionCampaign::query()->where('tenant_id', $tenant->id)->count();

        return view('admin.dashboard', compact('tenant', 'context', 'profiles', 'campaigns'));
    }
}
