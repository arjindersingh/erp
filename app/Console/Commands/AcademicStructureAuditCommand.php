<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Academics\Models\AcademicYearLock;
use App\Domains\Academics\Models\AcademicYearScopeAssignment;
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
        foreach (AcademicYearScopeAssignment::withoutGlobalScopes()->with(['academicYear', 'accessScope'])->cursor() as $assignment) {
            $compatible = $assignment->academicYear !== null && $assignment->accessScope !== null
                && (int) $assignment->academicYear->tenant_id === (int) $assignment->tenant_id
                && (int) $assignment->accessScope->tenant_id === (int) $assignment->tenant_id;
            $compatible ? $this->info("PASS  Academic-year assignment {$assignment->uuid} has compatible ownership") : $this->error("FAIL  Academic-year assignment {$assignment->uuid} crosses ownership boundaries");
            $failures += $compatible ? 0 : 1;
        }
        foreach (AcademicYearLock::withoutGlobalScopes()->with('academicYear')->cursor() as $lock) {
            $compatible = $lock->academicYear !== null && (int) $lock->academicYear->tenant_id === (int) $lock->tenant_id;
            $compatible ? $this->info("PASS  Academic-year lock {$lock->uuid} has compatible ownership") : $this->error("FAIL  Academic-year lock {$lock->uuid} crosses ownership boundaries");
            $failures += $compatible ? 0 : 1;
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
