<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Authentication\ActiveContext;
use App\Core\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProfileInterfaceSettingsController
{
    public function __invoke(Request $request, TenantContext $tenantContext, ActiveContext $context): View
    {
        $tenant = $tenantContext->requireTenant();

        return view('filament.shared.pages.interface-settings', [
            'tenant' => $tenant,
            'portal' => $context->portal,
        ]);
    }
}
