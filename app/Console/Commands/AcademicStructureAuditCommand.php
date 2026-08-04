<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Academics\Models\InstituteAuthorityAffiliation;
use Illuminate\Console\Command;

final class AcademicStructureAuditCommand extends Command
{
    protected $signature = 'erp:academic-structure-audit';

    protected $description = 'Validate the shared academic structure';

    public function handle(): int
    {
        $failures = 0;
        foreach (AcademicYear::withoutGlobalScopes()->cursor() as $year) {
            if (! $year->starts_on->lt($year->ends_on)) {
                $this->error("FAIL  Academic year {$year->code} has an invalid date range");
                $failures++;
            } else {
                $this->info("PASS  Academic year {$year->code} has a valid date range");
            }
        }
        foreach (InstituteAuthorityAffiliation::withoutGlobalScopes()->with('authority')->cursor() as $affiliation) {
            $compatible = $affiliation->authority->tenant_id === null || (int) $affiliation->authority->tenant_id === (int) $affiliation->tenant_id;
            $compatible ? $this->info("PASS  Affiliation {$affiliation->uuid} has compatible ownership") : $this->error("FAIL  Affiliation {$affiliation->uuid} crosses tenant ownership");
            $failures += $compatible ? 0 : 1;
        }
        if (AcademicYear::withoutGlobalScopes()->count() === 0) {
            $this->warn('WARNING  No academic years are configured');
        }

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }
}
