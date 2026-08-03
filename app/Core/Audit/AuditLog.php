<?php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Audit\Concerns\AppendOnly;
use App\Core\Authorization\AccessScope;
use App\Core\Authorization\Role;
use App\Core\Authorization\RoleAssignment;
use App\Core\Identity\Person;
use App\Core\Identity\UserMembership;
use App\Core\Modules\Module;
use App\Core\Modules\ModuleFeature;
use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\AuditLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UseFactory(AuditLogFactory::class)]
#[Fillable([
    'uuid', 'tenant_id', 'company_id', 'campus_id', 'institute_id', 'academic_year_id',
    'actor_type', 'actor_id', 'user_id', 'person_id', 'membership_id', 'access_scope_id',
    'role_assignment_id', 'role_id', 'portal_id', 'module_id', 'module_feature_id', 'module_code',
    'feature_code', 'category', 'action', 'severity', 'outcome', 'subject_type', 'subject_id',
    'subject_uuid', 'subject_label', 'event_code', 'event_title', 'event_summary', 'reason', 'remarks',
    'old_values_json', 'new_values_json', 'changed_fields_json', 'metadata_json', 'request_id',
    'correlation_id', 'batch_id', 'session_id', 'job_id', 'api_token_id', 'source', 'route_name',
    'request_method', 'request_url', 'ip_address', 'forwarded_ip', 'user_agent', 'device_type',
    'browser', 'operating_system', 'occurred_at', 'recorded_at', 'expires_at', 'archived_at',
    'integrity_hash', 'previous_hash',
])]
class AuditLog extends Model
{
    /** @use HasFactory<AuditLogFactory> */
    use AppendOnly, HasFactory, HasPublicUuid;

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'actor_type' => AuditActorType::class, 'category' => AuditCategory::class,
            'severity' => AuditSeverity::class, 'outcome' => AuditOutcome::class, 'source' => AuditSource::class,
            'old_values_json' => 'array', 'new_values_json' => 'array', 'changed_fields_json' => 'array',
            'metadata_json' => 'array', 'occurred_at' => 'immutable_datetime', 'recorded_at' => 'immutable_datetime',
            'expires_at' => 'immutable_datetime', 'archived_at' => 'immutable_datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(UserMembership::class, 'membership_id');
    }

    public function accessScope(): BelongsTo
    {
        return $this->belongsTo(AccessScope::class);
    }

    public function roleAssignment(): BelongsTo
    {
        return $this->belongsTo(RoleAssignment::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function moduleFeature(): BelongsTo
    {
        return $this->belongsTo(ModuleFeature::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return HasMany<AuditLogChange, $this> */
    public function changes(): HasMany
    {
        return $this->hasMany(AuditLogChange::class);
    }
}
