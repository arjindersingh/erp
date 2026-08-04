<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Authentication\AuthenticatedProfileResolver;
use App\Core\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProfileController
{
    public function __invoke(Request $request, TenantContext $tenantContext, AuthenticatedProfileResolver $resolver): View
    {
        $tenant = $tenantContext->requireTenant();
        $profiles = $resolver->resolveFor($request->user(), $tenant);

        return view('profile', compact('profiles'));
    }
}
