<?php

declare(strict_types=1);

namespace App\Core\Identity;

use App\Core\Authorization\AccessScope;
use App\Core\Identity\Exceptions\InvalidMembership;

class MembershipValidator
{
    public function prepareAndValidate(UserMembership $membership): void
    {
        $this->validateDates($membership);

        $scope = AccessScope::withTrashed()
            ->where('tenant_id', $membership->tenant_id)
            ->whereKey($membership->access_scope_id)
            ->first();

        if ($scope === null) {
            throw InvalidMembership::because('access scope does not belong to the membership tenant.');
        }

        if ($this->requiresActiveAssociations($membership) && ! $scope->isActive()) {
            throw InvalidMembership::because('access scope is not active.');
        }

        $this->validatePersonAndProfile($membership);
        $this->setActiveIdentityKey($membership);
    }

    private function validateDates(UserMembership $membership): void
    {
        if ($membership->starts_at !== null
            && $membership->ends_at !== null
            && $membership->ends_at->lessThanOrEqualTo($membership->starts_at)) {
            throw InvalidMembership::because('end date must be later than start date.');
        }
    }

    private function validatePersonAndProfile(UserMembership $membership): void
    {
        if ($membership->person_id === null) {
            if ($membership->membership_type->requiresPerson()) {
                throw InvalidMembership::because('membership type requires a tenant person.');
            }

            if ($membership->profile_id !== null) {
                throw InvalidMembership::because('profile cannot be selected without a person.');
            }

            return;
        }

        $person = Person::withTrashed()
            ->where('tenant_id', $membership->tenant_id)
            ->whereKey($membership->person_id)
            ->first();

        if ($person === null) {
            throw InvalidMembership::because('person does not belong to the membership tenant.');
        }

        if ($this->requiresActiveAssociations($membership)
            && ($person->trashed() || $person->status !== IdentityStatus::Active)) {
            throw InvalidMembership::because('person is not active.');
        }

        $linkQuery = UserPersonLink::query()
            ->where('tenant_id', $membership->tenant_id)
            ->where('user_id', $membership->user_id)
            ->where('person_id', $membership->person_id);

        if (! $linkQuery->exists()) {
            throw InvalidMembership::because('user is not linked to the tenant person.');
        }

        if ($this->requiresActiveAssociations($membership)
            && ! (clone $linkQuery)->where('status', IdentityStatus::Active->value)->exists()) {
            throw InvalidMembership::because('user-person link is not active.');
        }

        if ($membership->profile_id !== null) {
            $profile = Profile::withTrashed()
                ->where('tenant_id', $membership->tenant_id)
                ->where('person_id', $membership->person_id)
                ->whereKey($membership->profile_id)
                ->first();

            if ($profile === null) {
                throw InvalidMembership::because('profile does not belong to the membership person and tenant.');
            }

            if ($this->requiresActiveAssociations($membership) && ! $profile->isActiveAt()) {
                throw InvalidMembership::because('profile is not active.');
            }
        }
    }

    private function setActiveIdentityKey(UserMembership $membership): void
    {
        if ($membership->status !== MembershipStatus::Active) {
            $membership->setAttribute('active_identity_key', null);

            return;
        }

        $membership->setAttribute('active_identity_key', hash('sha256', implode('|', [
            $membership->tenant_id,
            $membership->user_id,
            $membership->access_scope_id,
            $membership->membership_type->value,
        ])));
    }

    private function requiresActiveAssociations(UserMembership $membership): bool
    {
        return in_array($membership->status, [
            MembershipStatus::Pending,
            MembershipStatus::Active,
        ], true);
    }
}
