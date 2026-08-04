<?php

declare(strict_types=1);

namespace App\Core\Attribution;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class ActorContextResolver
{
    private ?Request $request = null;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function resolve(): ActorContext
    {
        return $this->resolveFromRequest($this->request ?? request());
    }

    public function resolveFromRequest(Request $request): ActorContext
    {
        $this->request = $request;

        if ($request->user() !== null) {
            return $this->forAuthenticatedRequest();
        }

        if ($request->attributes->get('actor_context')) {
            return $request->attributes->get('actor_context');
        }

        $publicAccessIdentityId = $request->attributes->get('public_access_identity_id')
            ?? ($request->attributes->get('public_access_identity')?->getKey());

        return $this->forPublicRequest(false, $publicAccessIdentityId);
    }

    public function forAuthenticatedRequest(): ActorContext
    {
        $request = $this->request ?? request();
        $user = $request->user();

        return new ActorContext(
            actorType: ActorType::AuthenticatedUser,
            authenticationState: AuthenticationState::Authenticated,
            operationSource: $this->sourceForRequest($request),
            userId: $user?->getAuthIdentifier(),
            tenantId: $request->attributes->get('tenant')?->id,
            companyId: $request->attributes->get('company_id'),
            campusId: $request->attributes->get('campus_id'),
            instituteId: $request->attributes->get('institute_id'),
            portalId: $request->attributes->get('portal_id'),
            moduleId: $request->attributes->get('module_id'),
            requestId: $request->attributes->get('request_id'),
            correlationId: $request->attributes->get('correlation_id'),
            routeName: $request->route()?->getName(),
            httpMethod: $request->method(),
            ipAddressHash: $this->hashValue($request->ip()),
            userAgentSummary: $this->summarizeUserAgent($request->userAgent()),
        );
    }

    public function forPublicRequest(bool $verified = false, ?string $publicAccessIdentityId = null): ActorContext
    {
        $request = $this->request ?? request();

        return new ActorContext(
            actorType: $verified ? ActorType::PublicVerified : ActorType::PublicAnonymous,
            authenticationState: $verified ? AuthenticationState::PublicVerified : AuthenticationState::UnauthenticatedPublic,
            operationSource: $this->sourceForRequest($request),
            publicSessionId: $request->attributes->get('public_session_id'),
            publicAccessIdentityId: $publicAccessIdentityId,
            verificationMethod: $verified ? 'email_otp' : null,
            tenantId: $request->attributes->get('tenant')?->id,
            requestId: $request->attributes->get('request_id'),
            correlationId: $request->attributes->get('correlation_id'),
            routeName: $request->route()?->getName(),
            httpMethod: $request->method(),
            ipAddressHash: $this->hashValue($request->ip()),
            userAgentSummary: $this->summarizeUserAgent($request->userAgent()),
        );
    }

    public function forQueueJob(string $jobUuid, ?ActorContextSnapshot $initiatingContext = null): ActorContext
    {
        $request = $this->request ?? request();

        return new ActorContext(
            actorType: ActorType::QueuedJob,
            authenticationState: AuthenticationState::SystemInternal,
            operationSource: OperationSource::Queue,
            tenantId: $initiatingContext?->tenantId,
            companyId: $initiatingContext?->companyId,
            campusId: $initiatingContext?->campusId,
            instituteId: $initiatingContext?->instituteId,
            academicYearId: $initiatingContext?->academicYearId,
            portalId: $initiatingContext?->portalId,
            moduleId: $initiatingContext?->moduleId,
            requestId: $request->attributes->get('request_id'),
            correlationId: $request->attributes->get('correlation_id'),
            jobUuid: $jobUuid,
            initiatedByUserId: $initiatingContext?->userId,
            initiatedByActorType: $initiatingContext?->actorType,
        );
    }

    public function forScheduledTask(string $taskName, ?string $scheduleRunId = null): ActorContext
    {
        return new ActorContext(
            actorType: ActorType::ScheduledTask,
            authenticationState: AuthenticationState::SystemInternal,
            operationSource: OperationSource::Scheduler,
            requestId: (string) Str::uuid(),
            correlationId: $scheduleRunId,
            commandName: $taskName,
            scheduleRunId: $scheduleRunId,
        );
    }

    public function forImport(string $importBatchId, ActorContext $initiatedBy): ActorContext
    {
        return new ActorContext(
            actorType: ActorType::BulkImport,
            authenticationState: AuthenticationState::SystemInternal,
            operationSource: OperationSource::BulkImport,
            userId: $initiatedBy->userId,
            tenantId: $initiatedBy->tenantId,
            companyId: $initiatedBy->companyId,
            campusId: $initiatedBy->campusId,
            instituteId: $initiatedBy->instituteId,
            academicYearId: $initiatedBy->academicYearId,
            portalId: $initiatedBy->portalId,
            moduleId: $initiatedBy->moduleId,
            importBatchId: $importBatchId,
            initiatedByUserId: $initiatedBy->userId,
            initiatedByActorType: $initiatedBy->actorType,
            requestId: $initiatedBy->requestId,
            correlationId: $initiatedBy->correlationId,
        );
    }

    public function forIntegration(string $integrationId, ?string $webhookEventId = null): ActorContext
    {
        return new ActorContext(
            actorType: ActorType::Integration,
            authenticationState: AuthenticationState::ServiceAuthenticated,
            operationSource: OperationSource::Integration,
            integrationId: (int) $integrationId,
            webhookEventId: $webhookEventId !== null ? (int) $webhookEventId : null,
            requestId: (string) Str::uuid(),
            correlationId: $webhookEventId,
        );
    }

    public function forMigration(string $migrationBatchId): ActorContext
    {
        return new ActorContext(
            actorType: ActorType::DataMigration,
            authenticationState: AuthenticationState::SystemInternal,
            operationSource: OperationSource::Migration,
            migrationBatchId: $migrationBatchId,
            requestId: (string) Str::uuid(),
        );
    }

    private function sourceForRequest(Request $request): OperationSource
    {
        if ($request->is('api/*')) {
            return $request->user() !== null ? OperationSource::AuthenticatedApi : OperationSource::PublicApi;
        }

        return OperationSource::PublicWeb;
    }

    private function hashValue(?string $value): ?string
    {
        return $value === null ? null : hash('sha256', $value);
    }

    private function summarizeUserAgent(?string $userAgent): ?string
    {
        return $userAgent === null ? null : mb_substr($userAgent, 0, 160);
    }
}
