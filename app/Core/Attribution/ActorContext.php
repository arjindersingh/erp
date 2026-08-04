<?php

declare(strict_types=1);

namespace App\Core\Attribution;

use Illuminate\Support\Arr;

final readonly class ActorContext
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        public ActorType $actorType,
        public AuthenticationState $authenticationState,
        public OperationSource $operationSource,
        public ?int $userId = null,
        public ?int $personId = null,
        public ?int $membershipId = null,
        public ?int $employeeProfileId = null,
        public ?int $studentProfileId = null,
        public ?int $guardianProfileId = null,
        public ?int $impersonatorUserId = null,
        public ?int $impersonatorPersonId = null,
        public ?int $tenantId = null,
        public ?int $companyId = null,
        public ?int $campusId = null,
        public ?int $instituteId = null,
        public ?int $academicYearId = null,
        public ?int $portalId = null,
        public ?int $moduleId = null,
        public ?string $publicSessionId = null,
        public ?string $publicAccessIdentityId = null,
        public ?string $verificationMethod = null,
        public ?int $apiClientId = null,
        public ?int $integrationId = null,
        public ?int $webhookEventId = null,
        public ?string $importBatchId = null,
        public ?string $jobUuid = null,
        public ?string $scheduleRunId = null,
        public ?string $migrationBatchId = null,
        public ?int $initiatedByUserId = null,
        public ?ActorType $initiatedByActorType = null,
        public ?string $requestId = null,
        public ?string $correlationId = null,
        public ?string $routeName = null,
        public ?string $httpMethod = null,
        public ?string $commandName = null,
        public ?string $ipAddressHash = null,
        public ?string $userAgentSummary = null,
        public ?string $occurredAt = null,
        public array $context = [],
    ) {}

    public function toArray(): array
    {
        return $this->toAuditAttributes();
    }

    public function toAuditAttributes(): array
    {
        return [
            'actor_type' => $this->actorType->value,
            'authentication_state' => $this->authenticationState->value,
            'operation_source' => $this->operationSource->value,
            'user_id' => $this->userId,
            'person_id' => $this->personId,
            'membership_id' => $this->membershipId,
            'employee_profile_id' => $this->employeeProfileId,
            'student_profile_id' => $this->studentProfileId,
            'guardian_profile_id' => $this->guardianProfileId,
            'impersonator_user_id' => $this->impersonatorUserId,
            'impersonator_person_id' => $this->impersonatorPersonId,
            'tenant_id' => $this->tenantId,
            'company_id' => $this->companyId,
            'campus_id' => $this->campusId,
            'institute_id' => $this->instituteId,
            'academic_year_id' => $this->academicYearId,
            'portal_id' => $this->portalId,
            'module_id' => $this->moduleId,
            'public_session_id' => $this->publicSessionId,
            'public_access_identity_id' => $this->publicAccessIdentityId,
            'verification_method' => $this->verificationMethod,
            'api_client_id' => $this->apiClientId,
            'integration_id' => $this->integrationId,
            'webhook_event_id' => $this->webhookEventId,
            'import_batch_id' => $this->importBatchId,
            'job_uuid' => $this->jobUuid,
            'schedule_run_id' => $this->scheduleRunId,
            'migration_batch_id' => $this->migrationBatchId,
            'initiated_by_user_id' => $this->initiatedByUserId,
            'initiated_by_actor_type' => $this->initiatedByActorType?->value,
            'request_id' => $this->requestId,
            'correlation_id' => $this->correlationId,
            'route_name' => $this->routeName,
            'http_method' => $this->httpMethod,
            'command_name' => $this->commandName,
            'ip_address_hash' => $this->ipAddressHash,
            'user_agent_summary' => $this->userAgentSummary,
            'occurred_at' => $this->occurredAt,
            'metadata_json' => Arr::except($this->context, ['old_values', 'new_values']),
        ];
    }
}
