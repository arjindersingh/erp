<?php

declare(strict_types=1);

namespace App\Core\Platform;

use App\Core\Attribution\HasActorAttribution;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomainEvent extends Model
{
    use BelongsToTenant, HasActorAttribution, HasPublicUuid, SoftDeletes;

    protected $table = 'domain_events';

    protected $guarded = ['id'];

    public function getAttribute($key)
    {
        if ($key === 'updated_by_user_id' || $key === 'updated_actor_type' || $key === 'updated_authentication_state' || $key === 'updated_via' || $key === 'updated_request_id' || $key === 'updated_correlation_id') {
            return null;
        }

        return parent::getAttribute($key);
    }

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
            'metadata_json' => 'array',
            'actor_context_json' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function outboxMessages(): HasMany
    {
        return $this->hasMany(OutboxMessage::class, 'domain_event_id');
    }
}
