<?php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Authorization\Exceptions\InvalidRoleAssignment;
use App\Core\Identity\UserMembership;

class RoleAssignmentValidator
{
    public function validate(RoleAssignment $assignment): void
    {
        if ($assignment->starts_at !== null
            && $assignment->ends_at !== null
            && $assignment->ends_at->lessThanOrEqualTo($assignment->starts_at)) {
            throw InvalidRoleAssignment::because('end date must be later than start date.');
        }

        $membership = UserMembership::withTrashed()
            ->where('tenant_id', $assignment->tenant_id)
            ->where('user_id', $assignment->user_id)
            ->whereKey($assignment->user_membership_id)
            ->first();

        if ($membership === null) {
            throw InvalidRoleAssignment::because('membership does not belong to this user and tenant.');
        }

        if ($assignment->status === RoleAssignmentStatus::Active && ! $membership->isActiveAt()) {
            throw InvalidRoleAssignment::because('membership is not active.');
        }

        $membershipScope = AccessScope::withTrashed()
            ->where('tenant_id', $membership->tenant_id)
            ->whereKey($membership->access_scope_id)
            ->first();
        $assignmentScope = AccessScope::withTrashed()
            ->where('tenant_id', $assignment->tenant_id)
            ->whereKey($assignment->access_scope_id)
            ->first();

        if ($membershipScope === null
            || $assignmentScope === null
            || ! $membershipScope->containsScope($assignmentScope)) {
            throw InvalidRoleAssignment::because('scope must be the membership scope or one of its descendants.');
        }

        if ($assignment->status === RoleAssignmentStatus::Active && ! $assignmentScope->isActive()) {
            throw InvalidRoleAssignment::because('assignment scope is not active.');
        }

        $role = Role::query()->find($assignment->role_id);

        if ($role === null
            || ($role->tenant_id !== null && $role->tenant_id !== (int) $assignment->tenant_id)
            || ! $role->isAssignable()) {
            throw InvalidRoleAssignment::because('role is not assignable within this tenant.');
        }

        $this->setActiveIdentityKey($assignment);
    }

    private function setActiveIdentityKey(RoleAssignment $assignment): void
    {
        if ($assignment->status !== RoleAssignmentStatus::Active) {
            $assignment->setAttribute('active_identity_key', null);

            return;
        }

        $assignment->setAttribute('active_identity_key', hash('sha256', implode('|', [
            $assignment->user_membership_id,
            $assignment->role_id,
            $assignment->access_scope_id,
        ])));
    }
}
