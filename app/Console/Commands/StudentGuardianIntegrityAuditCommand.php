<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class StudentGuardianIntegrityAuditCommand extends Command
{
    protected $signature = 'erp:student-guardian-integrity-audit';

    protected $description = 'Audit student, guardian, and authority relationship integrity';

    public function handle(): int
    {
        $failures = 0;
        foreach (['student_categories', 'student_statuses', 'guardian_occupations', 'guardian_relationship_types', 'student_profiles', 'guardian_profiles', 'student_guardian_relationships'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("FAIL  Required table {$table} is missing");
                $failures++;
            } else {
                $this->info("PASS  Required table {$table} exists");
            }
        }
        if (Schema::hasTable('student_profiles')) {
            $invalid = DB::table('student_profiles as s')->leftJoin('persons as p', 'p.id', '=', 's.person_id')->leftJoin('student_statuses as ss', 'ss.id', '=', 's.student_status_id')->whereNull('s.deleted_at')->where(fn ($q) => $q->whereNull('p.id')->orWhereNull('ss.id')->orWhereColumn('p.tenant_id', '!=', 's.tenant_id'))->count();
            $failures += $this->critical('Student Profiles have valid Persons and statuses', $invalid);
            $failures += $this->critical('Student numbers are unique per tenant', $this->duplicateCount('student_profiles', ['tenant_id', 'student_number']));
            $failures += $this->critical('Persons have at most one Student Profile per tenant', $this->duplicateCount('student_profiles', ['tenant_id', 'person_id']));
        }
        if (Schema::hasTable('guardian_profiles')) {
            $invalid = DB::table('guardian_profiles as g')->leftJoin('persons as p', 'p.id', '=', 'g.person_id')->whereNull('g.deleted_at')->where(fn ($q) => $q->whereNull('p.id')->orWhereColumn('p.tenant_id', '!=', 'g.tenant_id'))->count();
            $failures += $this->critical('Guardian Profiles have valid Persons', $invalid);
            $failures += $this->critical('Persons have at most one Guardian Profile per tenant', $this->duplicateCount('guardian_profiles', ['tenant_id', 'person_id']));
        }
        if (Schema::hasTable('student_guardian_relationships')) {
            $invalid = DB::table('student_guardian_relationships as r')->leftJoin('student_profiles as s', 's.id', '=', 'r.student_profile_id')->leftJoin('guardian_profiles as g', 'g.id', '=', 'r.guardian_profile_id')->whereNull('r.deleted_at')->where(fn ($q) => $q->whereNull('s.id')->orWhereNull('g.id')->orWhereColumn('s.tenant_id', '!=', 'r.tenant_id')->orWhereColumn('g.tenant_id', '!=', 'r.tenant_id'))->count();
            $failures += $this->critical('Guardian relationships remain inside one tenant', $invalid);
            $duplicatePrimary = DB::query()->fromSub(DB::table('student_guardian_relationships')->select('tenant_id', 'student_profile_id')->whereNull('deleted_at')->where('status', 'active')->where('is_primary_guardian', true)->groupBy('tenant_id', 'student_profile_id')->havingRaw('COUNT(*) > 1'), 'duplicate_primary_guardians')->count();
            $failures += $this->critical('Primary guardian combinations are valid', $duplicatePrimary);
        }
        foreach (['profile_contacts', 'profile_addresses', 'student_documents', 'student_consents', 'student_user_links', 'guardian_user_links', 'student_institute_enrolments'] as $futureTable) {
            if (! Schema::hasTable($futureTable)) {
                $this->warn("WARNING  {$futureTable} is not installed in the current milestone");
            }
        }

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }

    /** @param list<string> $columns */
    private function duplicateCount(string $table, array $columns): int
    {
        return DB::query()->fromSub(DB::table($table)->select($columns)->whereNull('deleted_at')->groupBy($columns)->havingRaw('COUNT(*) > 1'), 'duplicates')->count();
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
