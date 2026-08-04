<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AttachPlatformContext
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('platform_context', [
            'request_id' => $request->attributes->get('request_id'),
            'correlation_id' => $request->attributes->get('correlation_id'),
        ]);

        return $next($request);
    }
}
