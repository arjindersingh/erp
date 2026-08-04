<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Core\Authentication\ActiveContext;
use App\Core\Authentication\AuthenticatedProfileResolver;
use App\Core\Authorization\EffectiveAccessService;
use App\Core\Modules\TenantModule;
use App\Core\Tenancy\TenantContext;
use Filament\Pages\Page;
use Illuminate\Http\Request;

final class AccessDiagnosticsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static string $view = 'filament.pages.access-diagnostics';

    public ?string $tenantName = null;

    public ?string $scopeName = null;

    public ?string $portalName = null;

    public ?string $academicYear = null;

    public ?string $personName = null;

    public function mount(Request $request, TenantContext $tenantContext, ActiveContext $context, AuthenticatedProfileResolver $resolver, EffectiveAccessService $access): void
    {
        $tenant = $tenantContext->requireTenant();
        $profiles = $resolver->resolveFor($request->user(), $tenant);

        $this->tenantName = $tenant->name;
        $this->scopeName = $context->scope->name;
        $this->portalName = $context->portal->name;
        $this->academicYear = $context->academicYear->name;
        $this->personName = $profiles->person?->display_name ?? 'No linked person';
    }
}
