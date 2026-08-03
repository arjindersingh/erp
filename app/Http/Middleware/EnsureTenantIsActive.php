<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Tenancy\TenantContext;
use App\Core\Tenancy\TenantStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class EnsureTenantIsActive
{
    public function __construct(private readonly TenantContext $tenantContext) {}

    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->tenantContext->tenant();
        if ($tenant === null) {
            return $next($request);
        }

        if (! $tenant->isActive()) {
            $status = $tenant->status === TenantStatus::Maintenance ? 503 : 403;
            throw new HttpException($status, 'This tenant is not currently available.');
        }

        return $next($request);
    }
}
