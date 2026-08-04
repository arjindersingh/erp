<?php

declare(strict_types=1);

namespace App\Core\Platform;

use App\Core\Tenancy\Tenant;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutboxMessage extends Model
{
    use BelongsToTenant, HasPublicUuid, SoftDeletes;

    protected $table = 'outbox_messages';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
            'headers_json' => 'array',
            'available_at' => 'datetime',
            'processed_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function domainEvent(): BelongsTo
    {
        return $this->belongsTo(DomainEvent::class, 'domain_event_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(OutboxDeliveryAttempt::class, 'outbox_message_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
