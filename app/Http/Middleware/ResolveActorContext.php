<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Attribution\ActorContextResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResolveActorContext
{
    public function __construct(private readonly ActorContextResolver $resolver)
    {
    }

    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        $context = $this->resolver->resolveFromRequest($request);
        $request->attributes->set('actor_context', $context);
        $request->attributes->set('actor_context_snapshot', $context->toArray());

        return $next($request);
    }
}
