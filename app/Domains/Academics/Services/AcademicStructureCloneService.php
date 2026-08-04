<?php

declare(strict_types=1);

namespace App\Domains\Academics\Services;

use App\Core\Audit\AuditEventDefinition;
use App\Core\Audit\AuditLogger;
use App\Core\Organization\Institute;
use App\Domains\Academics\Models\AcademicClass;
use App\Domains\Academics\Models\AcademicSection;
use App\Domains\Academics\Models\AcademicTerm;
use App\Domains\Academics\Models\AcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AcademicStructureCloneService
{
    public function __construct(private AuditLogger $audit) {}

    public function clone(AcademicYear $source, AcademicYear $destination, Institute $institute, array $components = ['classes', 'sections', 'terms']): array
    {
        if ((int) $source->tenant_id !== (int) $destination->tenant_id || (int) $source->tenant_id !== (int) $institute->tenant_id || $source->is($destination)) {
            throw ValidationException::withMessages(['destination' => 'Clone years and institute must share one tenant and use different years.']);
        }
        if ($destination->isReadOnly()) {
            throw ValidationException::withMessages(['destination' => 'Destination academic year is read-only.']);
        }

        $created = DB::transaction(function () use ($source, $destination, $institute, $components): array {
            $classMap = [];
            $created = ['classes' => 0, 'sections' => 0, 'terms' => 0];
            if (in_array('classes', $components, true)) {
                foreach (AcademicClass::withoutGlobalScopes()->where('tenant_id', $source->tenant_id)->where('institute_id', $institute->id)->where('academic_year_id', $source->id)->get() as $class) {
                    $clone = $class->replicate(['uuid', 'academic_year_id', 'created_at', 'updated_at', 'deleted_at']);
                    $clone->academic_year_id = $destination->id;
                    $clone->save();
                    $classMap[$class->id] = $clone->id;
                    $created['classes']++;
                }
            }
            if (in_array('sections', $components, true)) {
                foreach (AcademicSection::withoutGlobalScopes()->where('tenant_id', $source->tenant_id)->where('institute_id', $institute->id)->where('academic_year_id', $source->id)->whereNotNull('academic_class_id')->get() as $section) {
                    if (! isset($classMap[$section->academic_class_id])) {
                        continue;
                    } $clone = $section->replicate(['uuid', 'academic_year_id', 'academic_class_id', 'created_at', 'updated_at', 'deleted_at']);
                    $clone->academic_year_id = $destination->id;
                    $clone->academic_class_id = $classMap[$section->academic_class_id];
                    $clone->save();
                    $created['sections']++;
                }
            }
            if (in_array('terms', $components, true)) {
                foreach (AcademicTerm::withoutGlobalScopes()->where('tenant_id', $source->tenant_id)->where('institute_id', $institute->id)->where('academic_year_id', $source->id)->get() as $term) {
                    $clone = $term->replicate(['uuid', 'academic_year_id', 'created_at', 'updated_at', 'deleted_at']);
                    $clone->academic_year_id = $destination->id;
                    $clone->save();
                    $created['terms']++;
                }
            }

            return $created;
        });
        if (AuditEventDefinition::query()->where('event_code', 'academic.structure.cloned')->exists()) {
            $this->audit->success('academic.structure.cloned', $destination, [
                'source_academic_year_id' => $source->id, 'destination_academic_year_id' => $destination->id,
                'institute_id' => $institute->id, 'components' => $components, 'counts' => $created,
            ]);
        }

        return $created;
    }
}
