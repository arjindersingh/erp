<?php

declare(strict_types=1);

namespace App\Core\Platform;

use Illuminate\Support\Str;

final class DeadLetterService
{
    public function record(OutboxMessage $message, string $reason): void
    {
        $message->forceFill([
            'status' => 'dead_lettered',
            'failed_at' => now(),
            'last_error' => $reason,
        ])->save();

        $message->attempts()->create([
            'attempted_at' => now(),
            'status' => 'dead_lettered',
        ]);

        if ($message->domainEvent()->exists()) {
            $event = $message->domainEvent()->first();
            $event?->forceFill([
                'metadata_json' => array_merge($event->metadata_json ?? [], ['dead_letter_reason' => $reason]),
            ])->save();
        }

        DeadLetterMessage::query()->create([
            'uuid' => (string) Str::uuid(),
            'tenant_id' => $message->tenant_id,
            'outbox_message_id' => $message->id,
            'domain_event_id' => $message->domain_event_id,
            'reason' => $reason,
            'payload_json' => $message->payload_json,
            'status' => 'recorded',
        ]);
    }
}
