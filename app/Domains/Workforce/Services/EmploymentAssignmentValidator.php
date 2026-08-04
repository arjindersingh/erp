<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Services;

use App\Domains\Workforce\Models\EmployeeProfile;
use App\Domains\Workforce\Models\EmploymentAssignment;
use App\Domains\Workforce\Models\EmploymentStatus;
use App\Domains\Workforce\Models\JobPost;
use Illuminate\Validation\ValidationException;

final class EmploymentAssignmentValidator
{
    public function validate(EmploymentAssignment $assignment): void
    {
        $errors = [];
        $profile = EmployeeProfile::withoutGlobalScopes()->find($assignment->employee_profile_id);
        if (! $profile || (int) $profile->tenant_id !== (int) $assignment->tenant_id) {
            $errors['employee_profile_id'] = 'Employee profile must belong to the assignment tenant.';
        }
        if ($profile) {
            $active = EmploymentStatus::withoutGlobalScopes()->whereKey($profile->employment_status_id)->value('is_active_status');
            if (! $active) {
                $errors['employee_profile_id'] = 'An inactive employee cannot receive an assignment.';
            }
        }
        if ($assignment->ends_on && $assignment->starts_on && $assignment->ends_on < $assignment->starts_on) {
            $errors['ends_on'] = 'End date must be on or after start date.';
        }
        if ($assignment->job_post_id) {
            $post = JobPost::withoutGlobalScopes()->find($assignment->job_post_id);
            if (! $post || (int) $post->tenant_id !== (int) $assignment->tenant_id || (int) $post->designation_id !== (int) $assignment->designation_id || (int) $post->job_category_id !== (int) $assignment->job_category_id || ($post->department_id && (int) $post->department_id !== (int) $assignment->department_id) || ($post->institute_id && (int) $post->institute_id !== (int) $assignment->institute_id)) {
                $errors['job_post_id'] = 'Job post is incompatible with the assignment boundary or masters.';
            }
        }
        if ($assignment->is_primary && EmploymentAssignment::withoutGlobalScopes()->where('tenant_id', $assignment->tenant_id)->where('employee_profile_id', $assignment->employee_profile_id)->where('is_primary', true)->whereNot('id', $assignment->id ?? 0)->exists()) {
            $errors['is_primary'] = 'Only one primary assignment is allowed per employee.';
        }
        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
