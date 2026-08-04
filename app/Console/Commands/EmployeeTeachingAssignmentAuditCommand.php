<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class EmployeeTeachingAssignmentAuditCommand extends Command
{
    protected $signature = 'erp:employee-teaching-assignment-audit';

    protected $description = 'Audit employee and teaching assignment ownership and integrity';

    public function handle(): int
    {
        $failures = 0;
        foreach (['department_types', 'departments', 'designations', 'job_posts', 'employee_profiles', 'employment_assignments', 'employment_assignment_histories'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("FAIL  Required workforce table {$table} is missing");
                $failures++;
            } else {
                $this->info("PASS  Required workforce table {$table} exists");
            }
        }

        if (Schema::hasTable('employee_profiles')) {
            $invalidPeople = DB::table('employee_profiles as ep')->leftJoin('persons as p', 'p.id', '=', 'ep.person_id')->whereNull('ep.deleted_at')->where(fn ($q) => $q->whereNull('p.id')->orWhereColumn('p.tenant_id', '!=', 'ep.tenant_id'))->count();
            $failures += $this->critical('Employee Profiles have valid tenant-owned Persons', $invalidPeople);
            $duplicates = DB::query()->fromSub(
                DB::table('employee_profiles')->select(['tenant_id', 'person_id'])->whereNull('deleted_at')->groupBy(['tenant_id', 'person_id'])->havingRaw('COUNT(*) > 1'),
                'duplicate_employee_profiles',
            )->count();
            $failures += $this->critical('No duplicate active Employee Profiles', $duplicates);
        }
        if (Schema::hasTable('employment_assignments')) {
            $invalid = DB::table('employment_assignments as ea')->leftJoin('employee_profiles as ep', 'ep.id', '=', 'ea.employee_profile_id')->leftJoin('institutes as i', 'i.id', '=', 'ea.institute_id')->whereNull('ea.deleted_at')->where(fn ($q) => $q->whereNull('ep.id')->orWhereNull('i.id')->orWhereColumn('ep.tenant_id', '!=', 'ea.tenant_id')->orWhereColumn('i.tenant_id', '!=', 'ea.tenant_id')->orWhereColumn('i.company_id', '!=', 'ea.company_id')->orWhereColumn('i.campus_id', '!=', 'ea.campus_id'))->count();
            $failures += $this->critical('Employment Assignments have valid employee and institute boundaries', $invalid);
            $orphanHistories = DB::table('employment_assignment_histories as h')->leftJoin('employment_assignments as ea', 'ea.id', '=', 'h.employment_assignment_id')->whereNull('ea.id')->count();
            $failures += $this->critical('No orphaned Employment Assignment histories', $orphanHistories);
        }

        foreach (['teacher_profiles', 'academic_teaching_assignments', 'academic_coordination_assignments'] as $futureTable) {
            if (! Schema::hasTable($futureTable)) {
                $this->warn("WARNING  {$futureTable} is not installed in the current milestone");
            }
        }

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function critical(string $label, int $count): int
    {
        if ($count > 0) {
            $this->error("FAIL  {$label}: {$count} violation(s)");

            return 1;
        }
        $this->info("PASS  {$label}");

        return 0;
    }
}
