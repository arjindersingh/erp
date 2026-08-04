<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

final class AdmissionsIntegrityAuditCommand extends Command
{
    protected $signature = 'erp:admissions-integrity-audit';

    protected $description = 'Audit the integrity and public exposure of the Admissions module';

    public function handle(): int
    {
        $failures = 0;
        $warnings = 0;

        foreach (['admission_campaigns', 'admission_campaign_offerings', 'admission_applications'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->line("FAIL {$table}: table is missing.");
                $failures++;
            } else {
                $this->line("PASS {$table}: table is present.");
            }
        }

        if ($failures > 0) {
            return self::FAILURE;
        }

        $orphanApplications = DB::table('admission_applications as a')
            ->leftJoin('admission_campaigns as c', function ($join): void {
                $join->on('c.id', '=', 'a.campaign_id')->on('c.tenant_id', '=', 'a.tenant_id');
            })->whereNull('c.id')->count();
        $this->result('Applications without valid tenant campaign', $orphanApplications, $failures);

        $duplicateNumbers = DB::table('admission_applications')->whereNotNull('application_number')
            ->select('tenant_id', 'application_number')->groupBy('tenant_id', 'application_number')->havingRaw('COUNT(*) > 1')->count();
        $this->result('Duplicate application numbers', $duplicateNumbers, $failures);

        $invalidOfferingShapes = DB::table('admission_campaign_offerings')
            ->where(function ($query): void {
                $query->where(function ($q): void {
                    $q->where('offering_type', 'school_class')->whereNull('academic_class_id');
                })
                    ->orWhere(function ($q): void {
                        $q->where('offering_type', 'programme')->whereNull('programme_offering_id');
                    })
                    ->orWhereNotIn('offering_type', ['school_class', 'programme']);
            })->count();
        $this->result('Invalid campaign offering shapes', $invalidOfferingShapes, $failures);

        foreach (['admissions.public.campaigns', 'admissions.public.apply', 'admissions.public.applications.store'] as $routeName) {
            if (! Route::has($routeName)) {
                $this->line("FAIL route {$routeName}: missing.");
                $failures++;
            } else {
                $this->line("PASS route {$routeName}: registered.");
            }
        }

        $futureChecks = 13;
        $this->line("WARNING {$futureChecks} integrity checks are pending their schema milestones (scrutiny, scores, merit, seats, offers, conversion, audit and queues).");
        $warnings++;
        $this->newLine();
        $this->info("Admissions integrity audit: {$failures} failure(s), {$warnings} warning(s).");

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function result(string $label, int $count, int &$failures): void
    {
        if ($count > 0) {
            $this->line("FAIL {$label}: {$count}.");
            $failures++;
        } else {
            $this->line("PASS {$label}: none found.");
        }
    }
}
