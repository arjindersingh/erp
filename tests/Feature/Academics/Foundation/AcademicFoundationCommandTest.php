<?php

declare(strict_types=1);

namespace Tests\Feature\Academics\Foundation;

use App\Domains\Academics\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('academics')]
#[Group('foundation')]
#[Group('academic-structure')]
#[Group('audit')]
final class AcademicFoundationCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_academic_structure_audit_reports_valid_years_and_complete_canonical_schema(): void
    {
        AcademicYear::factory()->create(['code' => '2026-27']);
        $this->artisan('erp:academic-structure-audit')
            ->expectsOutputToContain('PASS  Academic year 2026-27 has a valid date range')
            ->expectsOutputToContain('PASS  Required canonical academic table academic_programmes exists')
            ->assertSuccessful();
    }
}
