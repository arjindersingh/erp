<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Attribution\ActorContext;
use App\Core\Attribution\ActorType;
use App\Core\Attribution\AuthenticationState;
use App\Core\Attribution\OperationSource;
use PHPUnit\Framework\TestCase;

final class ActorContextTest extends TestCase
{
    public function test_to_array_returns_a_snapshot_of_the_context(): void
    {
        $context = new ActorContext(
            actorType: ActorType::AuthenticatedUser,
            authenticationState: AuthenticationState::Authenticated,
            operationSource: OperationSource::PublicWeb,
            userId: 42,
            requestId: 'req-123',
            correlationId: 'corr-123',
        );

        $snapshot = $context->toArray();

        $this->assertSame('authenticated_user', $snapshot['actor_type']);
        $this->assertSame('authenticated', $snapshot['authentication_state']);
        $this->assertSame('public_web', $snapshot['operation_source']);
        $this->assertSame(42, $snapshot['user_id']);
        $this->assertSame('req-123', $snapshot['request_id']);
        $this->assertSame('corr-123', $snapshot['correlation_id']);
    }
}
