<?php

declare(strict_types=1);

namespace App\Core\Authentication;

use App\Core\Identity\Person;
use App\Core\Identity\Profile;
use App\Domains\Students\Models\GuardianProfile;
use App\Domains\Students\Models\StudentProfile;
use App\Domains\Workforce\Models\EmployeeProfile;

final readonly class AuthenticatedProfileSet
{
    /** @param list<Profile> $profiles */
    public function __construct(
        public ?Person $person,
        public array $profiles,
        public ?EmployeeProfile $employee,
        public ?StudentProfile $student,
        public ?GuardianProfile $guardian,
    ) {}
}
