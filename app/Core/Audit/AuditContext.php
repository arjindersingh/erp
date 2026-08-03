<?php

declare(strict_types=1);

namespace App\Core\Audit;

final readonly class AuditContext
{
    public function __construct(
        public ?int $tenantId = null,
        public ?int $companyId = null,
        public ?int $campusId = null,
        public ?int $instituteId = null,
        public ?int $academicYearId = null,
        public AuditActorType $actorType = AuditActorType::Unknown,
        public ?int $actorId = null,
        public ?int $userId = null,
        public ?int $personId = null,
        public ?int $membershipId = null,
        public ?int $accessScopeId = null,
        public ?int $roleAssignmentId = null,
        public ?int $roleId = null,
        public ?int $portalId = null,
        public ?string $sessionId = null,
        public ?string $requestId = null,
        public ?string $correlationId = null,
        public ?string $batchId = null,
        public ?string $jobId = null,
        public ?int $apiTokenId = null,
        public AuditSource $source = AuditSource::System,
        public ?string $routeName = null,
        public ?string $requestMethod = null,
        public ?string $requestUrl = null,
        public ?string $ipAddress = null,
        public ?string $forwardedIp = null,
        public ?string $userAgent = null,
    ) {}

    /** @return array<string, int|string|null> */
    public function toDatabase(): array
    {
        return [
            'tenant_id' => $this->tenantId, 'company_id' => $this->companyId, 'campus_id' => $this->campusId,
            'institute_id' => $this->instituteId, 'academic_year_id' => $this->academicYearId,
            'actor_type' => $this->actorType->value, 'actor_id' => $this->actorId, 'user_id' => $this->userId,
            'person_id' => $this->personId, 'membership_id' => $this->membershipId,
            'access_scope_id' => $this->accessScopeId, 'role_assignment_id' => $this->roleAssignmentId,
            'role_id' => $this->roleId, 'portal_id' => $this->portalId, 'session_id' => $this->sessionId,
            'request_id' => $this->requestId, 'correlation_id' => $this->correlationId, 'batch_id' => $this->batchId,
            'job_id' => $this->jobId, 'api_token_id' => $this->apiTokenId, 'source' => $this->source->value,
            'route_name' => $this->routeName, 'request_method' => $this->requestMethod,
            'request_url' => $this->requestUrl, 'ip_address' => $this->ipAddress,
            'forwarded_ip' => $this->forwardedIp, 'user_agent' => $this->userAgent,
        ];
    }
}
