<?php

declare(strict_types=1);

namespace App\Core\Platform;

use App\Core\Attribution\ActorContext;
use App\Core\Tenancy\CurrentTenant;
use App\Core\Tenancy\TenantContext;
use Illuminate\Support\Str;

final class DomainEventRecorder
{
    /** @param array<string, mixed> $payload */
    public function record(string $eventName, array $payload, ?string $aggregateType = null, ?string $aggregateId = null, ?array $metadata = null, ?ActorContext $actorContext = null): DomainEvent
    {
        $event = DomainEvent::query()->create([
            'uuid' => (string) Str::uuid(),
            'tenant_id' => $actorContext?->tenantId ?? $this->tenantIdFromContainer() ?? null,
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'event_name' => $eventName,
            'event_version' => 1,
            'payload_json' => $payload,
            'metadata_json' => $metadata ?? [],
            'actor_context_json' => $actorContext?->toAuditAttributes() ?? [],
            'request_id' => $actorContext?->requestId,
            'correlation_id' => $actorContext?->correlationId,
            'occurred_at' => now(),
        ]);

        $this->outbox($event);

        return $event;
    }

    private function tenantIdFromContainer(): ?int
    {
        if (app()->bound(CurrentTenant::class)) {
            return app(CurrentTenant::class)->id();
        }

        if (app()->bound(TenantContext::class)) {
            return app(TenantContext::class)->id();
        }

        return null;
    }

    private function outbox(DomainEvent $event): void
    {
        OutboxMessage::query()->create([
            'uuid' => (string) Str::uuid(),
            'tenant_id' => $event->tenant_id,
            'domain_event_id' => $event->id,
            'topic' => 'domain.events',
            'event_name' => $event->event_name,
            'event_version' => $event->event_version,
            'payload_json' => $event->payload_json,
            'headers_json' => ['correlation_id' => $event->correlation_id],
            'available_at' => now(),
            'attempt_count' => 0,
            'status' => 'pending',
        ]);
    }
}
