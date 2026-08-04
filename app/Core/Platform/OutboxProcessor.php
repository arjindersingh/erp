<?php

declare(strict_types=1);

namespace App\Core\Platform;

use Illuminate\Support\Facades\DB;

final class OutboxProcessor
{
    public function processPending(): int
    {
        $messages = OutboxMessage::query()
            ->where('status', 'pending')
            ->orderBy('available_at')
            ->limit(50)
            ->get();

        foreach ($messages as $message) {
            DB::transaction(function () use ($message): void {
                $message->forceFill([
                    'status' => 'processing',
                    'attempt_count' => $message->attempt_count + 1,
                ]);
                $message->save();

                OutboxDeliveryAttempt::query()->create([
                    'outbox_message_id' => $message->id,
                    'attempted_at' => now(),
                    'status' => 'attempted',
                ]);

                $message->forceFill([
                    'status' => 'processed',
                    'processed_at' => now(),
                ]);
                $message->save();
            });
        }

        return $messages->count();
    }
}
