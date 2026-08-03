<?php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Authorization\AccessStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

final class AuditLogger
{
    private ?AuditContext $context = null;

    private ?AuditActor $actor = null;

    private ?string $batchId = null;

    private ?string $reason = null;

    public function __construct(
        private readonly AuditContextFactory $contextFactory,
        private readonly SensitiveFieldRegistry $sensitiveFields,
    ) {}

    public function log(string $eventCode, ?Model $subject = null, array $metadata = []): AuditLog
    {
        return $this->record($eventCode, AuditOutcome::Success, $subject, $metadata);
    }

    public function success(string $eventCode, ?Model $subject = null, array $metadata = []): AuditLog
    {
        return $this->record($eventCode, AuditOutcome::Success, $subject, $metadata);
    }

    public function failed(string $eventCode, ?Model $subject = null, array $metadata = []): AuditLog
    {
        return $this->record($eventCode, AuditOutcome::Failed, $subject, $metadata);
    }

    public function denied(string $eventCode, ?Model $subject = null, array $metadata = []): AuditLog
    {
        return $this->record($eventCode, AuditOutcome::Denied, $subject, $metadata);
    }

    public function security(string $eventCode, array $metadata = []): AuditLog
    {
        return $this->record($eventCode, AuditOutcome::Denied, null, $metadata);
    }

    public function change(
        string $eventCode,
        Model $subject,
        array $oldValues,
        array $newValues,
        array $metadata = [],
    ): AuditLog {
        $metadata['old_values'] = $oldValues;
        $metadata['new_values'] = $newValues;

        return $this->record($eventCode, AuditOutcome::Success, $subject, $metadata);
    }

    public function forBatch(string $batchId): self
    {
        $clone = clone $this;
        $clone->batchId = $batchId;

        return $clone;
    }

    public function withActor(AuditActor $actor): self
    {
        $clone = clone $this;
        $clone->actor = $actor;

        return $clone;
    }

    public function withContext(AuditContext $context): self
    {
        $clone = clone $this;
        $clone->context = $context;

        return $clone;
    }

    public function withReason(string $reason): self
    {
        $clone = clone $this;
        $clone->reason = $reason;

        return $clone;
    }

    private function record(string $eventCode, AuditOutcome $outcome, ?Model $subject, array $metadata): AuditLog
    {
        if (! config('audit.enabled', true)) {
            throw new LogicException('Audit logging is disabled.');
        }

        $definition = AuditEventDefinition::query()
            ->where('event_code', $eventCode)
            ->where('status', AccessStatus::Active->value)
            ->firstOrFail();
        $context = $this->context ?? $this->contextFactory->make();
        $subjectTenantId = $this->subjectTenantId($subject);
        if ($context->tenantId !== null && $subjectTenantId !== null && $context->tenantId !== $subjectTenantId) {
            throw new LogicException('Cross-tenant audit subject rejected.');
        }
        $tenantId = $context->tenantId ?? $subjectTenantId;
        [$oldValues, $newValues, $changedFields, $changes] = $this->prepareChanges(
            $subject,
            Arr::pull($metadata, 'old_values', []),
            Arr::pull($metadata, 'new_values', []),
        );
        $occurredAt = Arr::pull($metadata, 'occurred_at', now());
        $reason = $this->reason ?? Arr::pull($metadata, 'reason');
        $remarks = Arr::pull($metadata, 'remarks');
        $summary = Arr::pull($metadata, 'summary', $definition->summary_template);
        $severity = Arr::pull($metadata, 'severity', $definition->default_severity);
        $severity = $severity instanceof AuditSeverity ? $severity : AuditSeverity::from((string) $severity);
        $moduleId = Arr::pull($metadata, 'module_id');
        $moduleFeatureId = Arr::pull($metadata, 'module_feature_id');
        $moduleCode = Arr::pull($metadata, 'module_code');
        $featureCode = Arr::pull($metadata, 'feature_code');
        $safeMetadata = $this->sanitizeMetadata($metadata);
        $uuid = (string) Str::uuid();

        return DB::transaction(function () use (
            $context, $tenantId, $definition, $outcome, $subject, $oldValues, $newValues,
            $changedFields, $changes, $occurredAt, $reason, $remarks, $summary, $severity,
            $moduleId, $moduleFeatureId, $moduleCode, $featureCode, $safeMetadata, $uuid,
        ): AuditLog {
            $previousHash = AuditLog::query()->where('tenant_id', $tenantId)->latest('id')->lockForUpdate()->value('integrity_hash');
            $attributes = array_merge($context->toDatabase(), [
                'uuid' => $uuid,
                'tenant_id' => $tenantId,
                'actor_type' => $this->actor?->type->value ?? $context->actorType->value,
                'actor_id' => $this->actor?->id ?? $context->actorId,
                'user_id' => $this->actor?->userId ?? $context->userId,
                'module_id' => $moduleId,
                'module_feature_id' => $moduleFeatureId,
                'module_code' => $moduleCode,
                'feature_code' => $featureCode,
                'category' => $definition->category->value,
                'action' => $definition->action,
                'severity' => $severity->value,
                'outcome' => $outcome->value,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'subject_uuid' => $subject?->getAttribute('uuid'),
                'subject_label' => $this->subjectLabel($subject),
                'event_code' => $definition->event_code,
                'event_title' => $definition->title_template,
                'event_summary' => $summary,
                'reason' => $reason,
                'remarks' => $remarks,
                'old_values_json' => $oldValues ?: null,
                'new_values_json' => $newValues ?: null,
                'changed_fields_json' => $changedFields ?: null,
                'metadata_json' => $safeMetadata ?: null,
                'batch_id' => $this->batchId ?? $context->batchId,
                'occurred_at' => $occurredAt,
                'recorded_at' => now(),
                'expires_at' => $definition->category->defaultRetentionDays() === null ? null : now()->addDays($definition->category->defaultRetentionDays()),
                'previous_hash' => $previousHash,
            ]);
            $attributes['integrity_hash'] = $this->integrityHash($previousHash, $attributes);
            $log = AuditLog::query()->create($attributes);

            foreach ($changes as $change) {
                $log->changes()->create($change);
            }

            return $log->load('changes');
        });
    }

    /** @return array{array<string, mixed>, array<string, mixed>, list<string>, list<array<string, mixed>>} */
    private function prepareChanges(?Model $subject, array $old, array $new): array
    {
        $modelClass = $subject !== null ? $subject::class : Model::class;
        $fields = array_values(array_unique(array_merge(array_keys($old), array_keys($new))));
        $safeOld = [];
        $safeNew = [];
        $changed = [];
        $changes = [];

        foreach ($fields as $field) {
            if ($this->sensitiveFields->exclude($field) || ($old[$field] ?? null) === ($new[$field] ?? null)) {
                continue;
            }
            $isSensitive = $this->sensitiveFields->isSensitive($modelClass, $field);
            $hashOnly = $this->sensitiveFields->hashOnly($field);
            $oldValue = $old[$field] ?? null;
            $newValue = $new[$field] ?? null;
            $safeOld[$field] = $hashOnly ? '[HASHED]' : ($isSensitive ? $this->sensitiveFields->mask($field, $oldValue) : $oldValue);
            $safeNew[$field] = $hashOnly ? '[HASHED]' : ($isSensitive ? $this->sensitiveFields->mask($field, $newValue) : $newValue);
            $changed[] = $field;
            $changes[] = [
                'field_name' => $field,
                'field_label' => str($field)->headline()->toString(),
                'old_value_text' => $this->text($safeOld[$field]),
                'new_value_text' => $this->text($safeNew[$field]),
                'old_value_hash' => $hashOnly && $oldValue !== null ? hash('sha256', (string) $oldValue) : null,
                'new_value_hash' => $hashOnly && $newValue !== null ? hash('sha256', (string) $newValue) : null,
                'data_type' => get_debug_type($newValue ?? $oldValue),
                'is_sensitive' => $isSensitive || $hashOnly,
                'is_masked' => $isSensitive || $hashOnly,
            ];
        }

        return [$safeOld, $safeNew, $changed, $changes];
    }

    private function subjectTenantId(?Model $subject): ?int
    {
        $value = $subject?->getAttribute('tenant_id');

        return is_numeric($value) ? (int) $value : null;
    }

    private function subjectLabel(?Model $subject): ?string
    {
        if ($subject === null) {
            return null;
        }

        foreach (['display_name', 'name', 'title', 'code', 'uuid'] as $field) {
            $value = $subject->getAttribute($field);
            if (is_scalar($value) && (string) $value !== '') {
                return class_basename($subject).': '.(string) $value;
            }
        }

        return class_basename($subject).' #'.$subject->getKey();
    }

    private function sanitizeMetadata(array $metadata): array
    {
        $safe = [];
        foreach ($metadata as $key => $value) {
            $field = (string) $key;
            if ($this->sensitiveFields->exclude($field)) {
                continue;
            }
            $safe[$key] = is_array($value)
                ? $this->sanitizeMetadata($value)
                : ($this->sensitiveFields->isSensitive(Model::class, $field) ? $this->sensitiveFields->mask($field, $value) : $value);
        }

        return $safe;
    }

    private function integrityHash(?string $previousHash, array $attributes): string
    {
        ksort($attributes);
        $payload = json_encode($attributes, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return hash((string) config('audit.integrity.algorithm', 'sha256'), ($previousHash ?? '').$payload);
    }

    private function text(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return is_scalar($value) ? (string) $value : json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}
