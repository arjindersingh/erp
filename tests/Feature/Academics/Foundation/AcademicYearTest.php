<?php

declare(strict_types=1);

namespace Tests\Feature\Academics\Foundation;

use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Models\AcademicYear;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('academics')]
#[Group('foundation')]
final class AcademicYearTest extends TestCase
{
    use RefreshDatabase;

    public function test_end_date_must_be_after_start_date(): void
    {
        $this->expectException(ValidationException::class);
        AcademicYear::factory()->create(['starts_on' => '2027-04-01', 'ends_on' => '2027-03-31']);
    }

    public function test_only_one_current_default_year_exists_per_scope(): void
    {
        $tenant = Tenant::factory()->create();
        AcademicYear::factory()->current()->for($tenant)->create(['code' => '2026']);

        $this->expectException(ValidationException::class);
        AcademicYear::factory()->current()->for($tenant)->create(['code' => '2027']);
    }

    public function test_locked_year_rejects_ordinary_updates(): void
    {
        $year = AcademicYear::factory()->locked()->create();

        $this->expectException(ValidationException::class);
        $year->update(['name' => 'Changed']);
    }

    public function test_academic_year_cannot_reference_an_institute_from_another_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $foreignInstitute = Institute::factory()->create();

        $this->expectException(QueryException::class);
        AcademicYear::factory()->for($tenant)->create([
            'company_id' => $foreignInstitute->company_id,
            'campus_id' => $foreignInstitute->campus_id,
            'institute_id' => $foreignInstitute->id,
        ]);
    }
}
