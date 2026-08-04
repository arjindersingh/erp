<?php

declare(strict_types=1);

namespace App\Core\Authentication;

use App\Core\Authorization\AccessScope;
use App\Core\Identity\UserMembership;
use App\Core\Navigation\Portal;
use App\Domains\Academics\Models\AcademicYear;

final readonly class ActiveContext
{
    public function __construct(
        public UserMembership $membership,
        public AccessScope $scope,
        public Portal $portal,
        public AcademicYear $academicYear,
    ) {}

    /** @return array<string, int|string> */
    public function sessionPayload(): array
    {
        return ['membership_uuid' => $this->membership->uuid, 'portal_code' => $this->portal->code, 'academic_year_uuid' => $this->academicYear->uuid];
    }
}
