<?php

declare(strict_types=1);

namespace App\Core\Platform;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class TransactionalOutboxWriter
{
    public function write(DomainEvent $event): OutboxMessage
    {
        return DB::transaction(function () use ($event): OutboxMessage {
            $event->save();

            return OutboxMessage::query()->create([
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
        });
    }
}
