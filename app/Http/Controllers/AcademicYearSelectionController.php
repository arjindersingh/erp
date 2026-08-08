<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Authentication\ActiveContext;
use App\Core\Authentication\ActiveContextService;
use App\Core\Tenancy\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AcademicYearSelectionController
{
    public function __invoke(Request $request, ActiveContext $context, ActiveContextService $contexts, TenantContext $tenantContext): RedirectResponse
    {
        $data = $request->validate([
            'academic_year' => ['required', 'uuid'],
        ]);

        $selected = $contexts->select(
            $request->user(),
            $tenantContext->requireTenant(),
            $context->membership->uuid,
            $context->portal->code,
            $data['academic_year'],
        );

        $request->session()->regenerate();
        $request->session()->put('active_context', $selected->sessionPayload());

        return back();
    }
}
