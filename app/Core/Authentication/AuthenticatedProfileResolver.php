<?php

declare(strict_types=1);

namespace App\Core\Authentication;

use App\Core\Identity\Person;
use App\Core\Identity\Profile;
use App\Core\Identity\UserPersonLink;
use App\Core\Tenancy\Tenant;
use App\Domains\Students\Models\GuardianProfile;
use App\Domains\Students\Models\StudentProfile;
use App\Domains\Workforce\Models\EmployeeProfile;
use App\Models\User;

final class AuthenticatedProfileResolver
{
    public function resolveFor(User $user, Tenant $tenant): AuthenticatedProfileSet
    {
        $link = UserPersonLink::withoutGlobalScopes()->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)->where('status', 'active')->first();
        $person = $link === null ? null : Person::withoutGlobalScopes()->find($link->person_id);
        $profiles = $person === null ? [] : Profile::withoutGlobalScopes()->where('tenant_id', $tenant->id)
            ->where('person_id', $person->id)->where('status', 'active')->get()->all();

        return new AuthenticatedProfileSet(
            $person,
            $profiles,
            $this->profile(EmployeeProfile::class, $tenant, $person),
            $this->profile(StudentProfile::class, $tenant, $person),
            $this->profile(GuardianProfile::class, $tenant, $person),
        );
    }

    /** @template T of \Illuminate\Database\Eloquent\Model @param class-string<T> $class @return T|null */
    private function profile(string $class, Tenant $tenant, ?Person $person): mixed
    {
        return $person === null ? null : $class::withoutGlobalScopes()->where('tenant_id', $tenant->id)
            ->where('person_id', $person->id)->first();
    }
}
