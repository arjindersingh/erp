<?php

namespace App\Http\Middleware;

use App\Core\Tenancy\Exceptions\TenantCouldNotBeResolved;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantContext;
use App\Core\Tenancy\TenantResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolvePublicTenant
{
    public function __construct(
        protected TenantContext $tenantContext,
        protected TenantResolver $tenantResolver,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->tenantContext->clear();

        if ($this->tenantResolver->isCentralDomain($request) && config('tenancy.public_resolution.allow_central_domains', true)) {
            return $next($request);
        }

        $domain = $this->tenantResolver->resolvePublicTenant($request);

        if ($domain !== null && $domain->tenant instanceof Tenant) {
            $this->tenantContext->activate($domain->tenant, $domain);
            $request->attributes->set('tenant', $domain->tenant);
            $request->attributes->set('tenant_domain', $domain);

            return $next($request);
        }

        if (config('tenancy.public_resolution.fail_on_unknown_domain', true)) {
            throw TenantCouldNotBeResolved::forHost($request->getHost());
        }

        return $next($request);
    }
}
