<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Core\Authentication\ActiveContextService;
use App\Core\Identity\UserMembership;
use App\Core\Navigation\Portal;
use App\Core\Tenancy\TenantContext;
use App\Domains\Academics\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ContextSelectionController
{
    public function create(Request $request): View
    {
        $tenant = app(TenantContext::class)->requireTenant();
        $memberships = UserMembership::withoutGlobalScopes()->with('accessScope')->where('tenant_id', $tenant->id)
            ->where('user_id', $request->user()->id)->selectable()->get();
        $allowedPortalCodes = $memberships->flatMap(
            fn (UserMembership $membership): array => $membership->metadata['portal_codes'] ?? ['administration'],
        )->unique()->values();
        $portals = Portal::query()->where('status', 'active')->whereIn('code', $allowedPortalCodes)->get();
        $years = AcademicYear::withoutGlobalScopes()->where('tenant_id', $tenant->id)->whereIn('status', ['active', 'locked'])->get();

        return view('context.select', compact('tenant', 'memberships', 'portals', 'years'));
    }

    public function store(Request $request, ActiveContextService $service): RedirectResponse
    {
        $data = $request->validate(['membership' => ['required', 'uuid'], 'portal' => ['required', 'string'], 'academic_year' => ['required', 'uuid']]);
        $context = $service->select($request->user(), app(TenantContext::class)->requireTenant(), $data['membership'], $data['portal'], $data['academic_year']);
        $request->session()->regenerate();
        $request->session()->put('active_context', $context->sessionPayload());

        return redirect()->route('portal.dashboard');
    }
}
