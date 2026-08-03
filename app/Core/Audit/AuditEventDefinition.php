<?php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Authorization\AccessStatus;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\AuditEventDefinitionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[UseFactory(AuditEventDefinitionFactory::class)]
#[Fillable(['uuid', 'event_code', 'category', 'action', 'default_severity', 'title_template', 'summary_template', 'is_security_event', 'is_sensitive', 'is_required', 'retention_policy_id', 'notification_rule_id', 'status'])]
class AuditEventDefinition extends Model
{
    /** @use HasFactory<AuditEventDefinitionFactory> */
    use HasFactory, HasPublicUuid;

    protected $attributes = [
        'default_severity' => AuditSeverity::Info->value,
        'is_security_event' => false,
        'is_sensitive' => false,
        'is_required' => false,
        'status' => AccessStatus::Active->value,
    ];

    protected static function booted(): void
    {
        static::deleting(function (AuditEventDefinition $definition): void {
            if ($definition->is_required) {
                throw new LogicException('Required audit event definitions cannot be deleted.');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'category' => AuditCategory::class,
            'default_severity' => AuditSeverity::class,
            'is_security_event' => 'boolean',
            'is_sensitive' => 'boolean',
            'is_required' => 'boolean',
            'status' => AccessStatus::class,
        ];
    }

    /** @return HasMany<AuditLog, $this> */
    public function logs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'event_code', 'event_code');
    }
}
