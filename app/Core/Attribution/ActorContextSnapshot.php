<?php

declare(strict_types=1);

namespace App\Core\Attribution;

final readonly class ActorContextSnapshot
{
    public function __construct(
        public ActorType $actorType,
        public AuthenticationState $authenticationState,
        public OperationSource $operationSource,
        public ?int $userId = null,
        public ?int $personId = null,
        public ?int $membershipId = null,
        public ?int $impersonatorUserId = null,
        public ?int $tenantId = null,
        public ?int $companyId = null,
        public ?int $campusId = null,
        public ?int $instituteId = null,
        public ?int $academicYearId = null,
        public ?int $portalId = null,
        public ?int $moduleId = null,
        public ?string $requestId = null,
        public ?string $correlationId = null,
        public ?string $publicSessionId = null,
        public ?string $publicAccessIdentityId = null,
        public ?int $integrationId = null,
        public ?string $importBatchId = null,
    ) {}
}
