<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class FrontendUatAuditCommand extends Command
{
    protected $signature = 'erp:frontend-uat-audit';

    protected $description = 'Run the implemented frontend UAT integrity audits';

    public function handle(): int
    {
        $failures = 0;
        foreach (['erp:frontend-access-audit', 'erp:profile-resolution-audit', 'erp:navigation-audit', 'erp:admissions-integrity-audit'] as $command) {
            $code = Artisan::call($command);
            $this->line(Artisan::output());
            $failures += $code === 0 ? 0 : 1;
        }
        $this->line('WARNING Browser certification and operational admissions journeys are not complete.');
        $this->line($failures === 0 ? 'CERTIFIED WITH CONDITIONS: Milestone 1 foundation only.' : 'NOT CERTIFIED.');

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }
}
