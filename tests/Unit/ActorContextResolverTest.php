<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Attribution\ActorContextResolver;
use App\Core\Attribution\ActorType;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

final class ActorContextResolverTest extends TestCase
{
    public function test_it_builds_a_public_actor_context_from_a_request(): void
    {
        $request = Request::create('/admissions/apply', 'POST');
        $request->attributes->set('request_id', 'req-123');
        $request->attributes->set('correlation_id', 'corr-123');

        $resolver = new ActorContextResolver();
        $context = $resolver->resolveFromRequest($request);

        $this->assertSame(ActorType::PublicAnonymous, $context->actorType);
        $this->assertSame('req-123', $context->requestId);
        $this->assertSame('corr-123', $context->correlationId);
    }
}
