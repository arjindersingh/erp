<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\AcademicYear\AcademicYearContext;
use App\Core\Authentication\ActiveContext;
use App\Core\Authentication\ActiveContextService;
use App\Core\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RestoreActiveContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = app(TenantContext::class)->tenant();
        $payload = $request->session()->get('active_context');
        if ($request->user() === null) {
            abort(403, 'An active institutional context is required.');
        }

        if ($tenant === null) {
            return redirect()->route('platform.setup');
        }

        if (! is_array($payload)) {
            return redirect()->route('context.select');
        }

        try {
            $context = app(ActiveContextService::class)->select(
                $request->user(), $tenant, (string) ($payload['membership_uuid'] ?? ''),
                (string) ($payload['portal_code'] ?? ''), (string) ($payload['academic_year_uuid'] ?? ''),
            );
        } catch (\Throwable) {
            $request->session()->forget('active_context');

            return redirect()->route('context.select');
        }
        app()->instance(ActiveContext::class, $context);
        app(AcademicYearContext::class)->activate($context->academicYear, $context->scope);

        return $next($request);
    }
}
