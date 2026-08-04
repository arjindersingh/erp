<?php

declare(strict_types=1);

namespace App\Core\Platform;

use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeadLetterMessage extends Model
{
    use BelongsToTenant, HasPublicUuid, SoftDeletes;

    protected $table = 'dead_letter_messages';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
        ];
    }

    public function outboxMessage(): BelongsTo
    {
        return $this->belongsTo(OutboxMessage::class, 'outbox_message_id');
    }

    public function domainEvent(): BelongsTo
    {
        return $this->belongsTo(DomainEvent::class, 'domain_event_id');
    }
}
