<?php

declare(strict_types=1);

namespace App\Core\Attribution;

use App\Core\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

final class AuditEventWriter
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function record(
        string $eventType,
        ActorContext $actorContext,
        ?Model $auditable = null,
        array $oldValues = [],
        array $newValues = [],
        array $metadata = [],
    ): mixed {
        return $this->auditLogger
            ->withContext($this->contextFor($actorContext))
            ->change(
                $eventType,
                $auditable ?? new class extends Model {},
                $oldValues,
                $newValues,
                array_merge($metadata, $actorContext->toAuditAttributes()),
            );
    }

    public function recordRecordCreated(Model $model, ActorContext $actorContext): mixed
    {
        return $this->record('record.created', $actorContext, $model, [], $model->getAttributes(), [
            'event_category' => AuditEventCategory::RecordLifecycle->value,
        ]);
    }

    public function recordRecordUpdated(Model $model, array $oldValues, array $newValues, ActorContext $actorContext): mixed
    {
        return $this->record('record.updated', $actorContext, $model, $oldValues, $newValues, [
            'event_category' => AuditEventCategory::RecordLifecycle->value,
        ]);
    }

    public function recordAccessEvent(Model $model, string $eventType, ActorContext $actorContext, array $metadata = []): mixed
    {
        return $this->record($eventType, $actorContext, $model, [], [], array_merge([
            'event_category' => AuditEventCategory::Security->value,
        ], $metadata));
    }

    private function contextFor(ActorContext $actorContext): \App\Core\Audit\AuditContext
    {
        return new \App\Core\Audit\AuditContext(
            tenantId: $actorContext->tenantId,
            companyId: $actorContext->companyId,
            campusId: $actorContext->campusId,
            instituteId: $actorContext->instituteId,
            academicYearId: $actorContext->academicYearId,
            actorType: match ($actorContext->actorType) {
                ActorType::AuthenticatedUser => \App\Core\Audit\AuditActorType::User,
                ActorType::PublicAnonymous, ActorType::PublicVerified => \App\Core\Audit\AuditActorType::Unknown,
                ActorType::QueuedJob => \App\Core\Audit\AuditActorType::QueueWorker,
                ActorType::ScheduledTask => \App\Core\Audit\AuditActorType::ScheduledJob,
                ActorType::BulkImport => \App\Core\Audit\AuditActorType::Unknown,
                ActorType::Integration, ActorType::Webhook => \App\Core\Audit\AuditActorType::Integration,
                ActorType::System => \App\Core\Audit\AuditActorType::System,
                default => \App\Core\Audit\AuditActorType::Unknown,
            },
            actorId: $actorContext->userId,
            userId: $actorContext->userId,
            personId: $actorContext->personId,
            membershipId: $actorContext->membershipId,
            portalId: $actorContext->portalId,
            requestId: $actorContext->requestId,
            correlationId: $actorContext->correlationId,
            batchId: $actorContext->importBatchId,
            jobId: $actorContext->jobUuid,
            source: match ($actorContext->operationSource) {
                OperationSource::PublicWeb, OperationSource::PublicApi => \App\Core\Audit\AuditSource::Web,
                OperationSource::AuthenticatedApi => \App\Core\Audit\AuditSource::Api,
                OperationSource::Queue => \App\Core\Audit\AuditSource::Queue,
                OperationSource::Scheduler => \App\Core\Audit\AuditSource::Scheduler,
                OperationSource::BulkImport => \App\Core\Audit\AuditSource::Import,
                OperationSource::Integration, OperationSource::Webhook => \App\Core\Audit\AuditSource::Integration,
                OperationSource::Console => \App\Core\Audit\AuditSource::Console,
                default => \App\Core\Audit\AuditSource::System,
            },
            routeName: $actorContext->routeName,
            requestMethod: $actorContext->httpMethod,
            ipAddress: $actorContext->ipAddressHash,
            userAgent: $actorContext->userAgentSummary,
        );
    }
}
