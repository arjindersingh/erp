<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Platform\DeadLetterMessage;
use App\Core\Platform\OutboxMessage;
use Illuminate\Console\Command;

class OutboxIntegrityAuditCommand extends Command
{
    protected $signature = 'erp:outbox-integrity-audit';

    protected $description = 'Audit outbox delivery health and dead-letter storage.';

    public function handle(): int
    {
        $pending = OutboxMessage::query()->whereIn('status', ['pending', 'processing', 'retrying'])->count();
        $deadLetters = DeadLetterMessage::query()->count();

        if ($pending > 0 || $deadLetters === 0) {
            $this->warn('WARNING Outbox audit found pending items or missing dead letters.');
            return self::FAILURE;
        }

        $this->info('PASS Outbox integrity audit passed.');
        return self::SUCCESS;
    }
}
