<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Enums\AcademicEntityKey;
use App\Domains\Academics\Models\AcademicNomenclatureSetting;
use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Academics\Models\EducationAuthority;
use App\Domains\Academics\Models\EducationLevel;
use Illuminate\Database\Seeder;

final class AcademicFoundationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['PRE_PRIMARY', 'Pre-Primary', 'pre_primary', 10], ['PRIMARY', 'Primary', 'primary', 20],
            ['MIDDLE', 'Middle', 'middle', 30], ['SECONDARY', 'Secondary', 'secondary', 40],
            ['SENIOR_SECONDARY', 'Senior Secondary', 'senior_secondary', 50], ['CERTIFICATE', 'Certificate', 'certificate', 60],
            ['DIPLOMA', 'Diploma', 'diploma', 70], ['UNDERGRADUATE', 'Undergraduate', 'undergraduate', 80],
            ['POSTGRADUATE', 'Postgraduate', 'postgraduate', 90], ['DOCTORAL', 'Doctoral', 'doctoral', 100],
            ['PROFESSIONAL', 'Professional', 'professional', 110], ['VOCATIONAL', 'Vocational', 'vocational', 120],
        ] as [$code, $name, $category, $sequence]) {
            EducationLevel::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => $code], [
                'name' => $name, 'level_category' => $category, 'sequence' => $sequence, 'is_system' => true, 'status' => 'active',
            ]);
        }

        foreach ([['CBSE', 'Central Board of Secondary Education', 'school_board'], ['UGC', 'University Grants Commission', 'regulatory_body'], ['NCTE', 'National Council for Teacher Education', 'professional_council']] as [$code, $name, $type]) {
            EducationAuthority::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => $code], [
                'name' => $name, 'authority_type' => $type, 'is_system' => true, 'status' => 'active',
            ]);
        }

        foreach (Tenant::query()->get() as $tenant) {
            AcademicYear::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'company_id' => null, 'campus_id' => null, 'institute_id' => null, 'code' => '2026-2027'],
                ['name' => '2026–27', 'starts_on' => '2026-04-01', 'ends_on' => '2027-03-31', 'is_current' => true, 'is_default' => true, 'status' => 'active'],
            );
            foreach (AcademicEntityKey::cases() as $entity) {
                $singular = str($entity->value)->headline()->toString();
                AcademicNomenclatureSetting::withoutGlobalScopes()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'company_id' => null, 'campus_id' => null, 'institute_id' => null, 'entity_key' => $entity->value, 'locale' => 'en'],
                    ['singular_label' => $singular, 'plural_label' => str($singular)->plural()->toString(), 'status' => 'active'],
                );
            }
        }
    }
}
