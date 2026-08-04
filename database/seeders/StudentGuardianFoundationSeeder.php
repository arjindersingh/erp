<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Students\Models\GuardianOccupation;
use App\Domains\Students\Models\GuardianRelationshipType;
use App\Domains\Students\Models\StudentCategory;
use App\Domains\Students\Models\StudentStatus;
use Illuminate\Database\Seeder;

final class StudentGuardianFoundationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['general', 'scholarship', 'sports', 'staff_child', 'sibling', 'international', 'exchange', 'alumni_child', 'management_quota', 'reserved_category', 'special_support', 'other'] as $category) {
            StudentCategory::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => strtoupper($category)], ['name' => str($category)->headline(), 'category_type' => $category, 'is_system' => true, 'status' => 'active']);
        }
        foreach ([['prospective', false, false, true, false, false], ['applicant', true, false, true, false, true], ['provisionally_admitted', true, false, true, true, true], ['active', true, false, true, true, true], ['on_leave', true, false, true, true, true], ['suspended', false, false, false, false, true], ['withdrawn', false, true, false, false, false], ['transferred', false, true, false, false, false], ['completed', false, true, false, true, false], ['alumni', false, true, false, true, false], ['cancelled', false, true, false, false, false], ['deceased', false, true, false, false, false]] as [$code, $active, $terminal, $enrolment, $portal, $financial]) {
            StudentStatus::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => strtoupper($code)], ['name' => str($code)->headline(), 'is_active_status' => $active, 'is_terminal_status' => $terminal, 'allows_enrolment' => $enrolment, 'allows_portal_access' => $portal, 'allows_financial_activity' => $financial, 'is_system' => true, 'status' => 'active']);
        }
        foreach (['government_service', 'private_service', 'business', 'self_employed', 'agriculture', 'professional', 'homemaker', 'retired', 'unemployed', 'other'] as $occupation) {
            GuardianOccupation::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => strtoupper($occupation)], ['name' => str($occupation)->headline(), 'is_system' => true, 'status' => 'active']);
        }
        foreach ([['father', true, true], ['mother', true, true], ['legal_guardian', false, true], ['grandfather', false, false], ['grandmother', false, false], ['brother', false, false], ['sister', false, false], ['uncle', false, false], ['aunt', false, false], ['foster_guardian', false, true], ['sponsor', false, false], ['local_guardian', false, false], ['other', false, false]] as [$code, $parent, $legal]) {
            GuardianRelationshipType::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => strtoupper($code)], ['name' => str($code)->headline(), 'is_parent_relationship' => $parent, 'is_legal_relationship' => $legal, 'is_system' => true, 'status' => 'active']);
        }
    }
}
