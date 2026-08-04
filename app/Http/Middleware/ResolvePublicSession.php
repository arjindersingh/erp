<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Attribution\PublicAccessIdentity;
use App\Core\Attribution\PublicSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class ResolvePublicSession
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('storage/*') || $request->is('build/*') || $request->is('favicon.ico')) {
            return $next($request);
        }

        $requestId = (string) ($request->attributes->get('request_id') ?? Str::uuid());
        $identity = $request->attributes->get('public_access_identity');
        if ($identity instanceof PublicAccessIdentity) {
            $session = PublicSession::query()->create([
                'uuid' => (string) Str::uuid(),
                'tenant_id' => $request->attributes->get('tenant')?->id,
                'public_access_identity_id' => $identity->id,
                'session_identifier_hash' => hash('sha256', $requestId),
                'first_request_id' => $requestId,
                'last_request_id' => $requestId,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
                'first_route_name' => $request->route()?->getName(),
                'last_route_name' => $request->route()?->getName(),
                'ip_hash' => hash('sha256', $request->ip() ?? 'unknown'),
                'user_agent_summary' => mb_substr($request->userAgent() ?? '', 0, 160),
                'verification_state' => 'verified',
                'expires_at' => now()->addHours(12),
            ]);
            $request->attributes->set('public_session_id', $session->uuid);
        }

        return $next($request);
    }
}
