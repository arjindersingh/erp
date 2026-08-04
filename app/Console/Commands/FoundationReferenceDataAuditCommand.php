<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Platform\ReferenceGroup;
use App\Core\Platform\ReferenceValue;
use Illuminate\Console\Command;

class FoundationReferenceDataAuditCommand extends Command
{
    protected $signature = 'erp:reference-data-audit';

    protected $description = 'Audit the reference data foundation for tenant-safe availability.';

    public function handle(): int
    {
        $groups = ReferenceGroup::query()->count();
        $values = ReferenceValue::query()->count();

        if ($groups === 0 || $values === 0) {
            $this->warn('WARNING Reference data foundation is incomplete.');
            return self::FAILURE;
        }

        $this->info('PASS Reference data foundation is available.');
        return self::SUCCESS;
    }
}
