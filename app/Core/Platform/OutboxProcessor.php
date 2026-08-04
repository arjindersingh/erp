<?php

declare(strict_types=1);

namespace App\Core\Platform;

use Closure;
use Illuminate\Support\Facades\DB;

final class OutboxProcessor
{
    public function __construct(private readonly DeadLetterService $deadLetterService) {}

    public function processPending(?Closure $handler = null): int
    {
        $messages = OutboxMessage::query()
            ->whereIn('status', ['pending', 'retrying'])
            ->orderBy('available_at')
            ->limit(50)
            ->get();

        foreach ($messages as $message) {
            try {
                DB::transaction(function () use ($message, $handler): void {
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

                    if ($handler !== null) {
                        $handler($message);
                    }

                    $message->forceFill([
                        'status' => 'processed',
                        'processed_at' => now(),
                    ]);
                    $message->save();
                });
            } catch (\Throwable $exception) {
                $this->deadLetterService->record($message, $exception->getMessage());
            }
        }

        return $messages->count();
    }
}
