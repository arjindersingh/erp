<?php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Audit\Concerns\AppendOnly;
use Database\Factories\AuditLogChangeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(AuditLogChangeFactory::class)]
#[Fillable(['audit_log_id', 'field_name', 'field_label', 'old_value_text', 'new_value_text', 'old_value_hash', 'new_value_hash', 'data_type', 'is_sensitive', 'is_masked'])]
class AuditLogChange extends Model
{
    /** @use HasFactory<AuditLogChangeFactory> */
    use AppendOnly, HasFactory;

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['is_sensitive' => 'boolean', 'is_masked' => 'boolean'];
    }

    /** @return BelongsTo<AuditLog, $this> */
    public function auditLog(): BelongsTo
    {
        return $this->belongsTo(AuditLog::class);
    }
}
