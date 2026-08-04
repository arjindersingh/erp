<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class AssignRequestId
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->headers->get('X-Request-ID') ?: (string) Str::uuid();
        $request->attributes->set('request_id', $requestId);
        $request->attributes->set('correlation_id', $request->attributes->get('correlation_id') ?: $requestId);

        $response = $next($request);
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
