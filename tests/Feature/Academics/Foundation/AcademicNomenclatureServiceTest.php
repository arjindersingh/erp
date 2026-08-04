<?php

declare(strict_types=1);

namespace Tests\Feature\Academics\Foundation;

use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantContext;
use App\Domains\Academics\Models\AcademicNomenclatureSetting;
use App\Domains\Academics\Services\AcademicNomenclatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('academics')]
#[Group('foundation')]
final class AcademicNomenclatureServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_institute_label_overrides_tenant_label_and_defaults_are_stable(): void
    {
        $institute = Institute::factory()->create();
        $tenant = Tenant::query()->findOrFail($institute->tenant_id);
        app(TenantContext::class)->activate($tenant);
        AcademicNomenclatureSetting::factory()->for($tenant)->create(['entity_key' => 'section', 'singular_label' => 'Section']);
        AcademicNomenclatureSetting::factory()->for($tenant)->create([
            'company_id' => $institute->company_id, 'campus_id' => $institute->campus_id, 'institute_id' => $institute->id,
            'entity_key' => 'section', 'singular_label' => 'Batch', 'plural_label' => 'Batches',
        ]);
        $service = app(AcademicNomenclatureService::class);

        $this->assertSame('Batch', $service->label('section', false, $institute->company_id, $institute->campus_id, $institute->id));
        $this->assertSame('Subject', $service->subjectLabel());
    }
}
