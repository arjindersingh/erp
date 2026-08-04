<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Authentication\ActiveContext;
use App\Core\Authorization\EffectiveAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequireEffectivePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        abort_unless(app(EffectiveAccessService::class)->allows($request->user(), app(ActiveContext::class), $permission), 403);

        return $next($request);
    }
}
