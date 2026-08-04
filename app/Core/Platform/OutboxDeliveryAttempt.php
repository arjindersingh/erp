<?php

declare(strict_types=1);

namespace App\Core\Platform;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutboxDeliveryAttempt extends Model
{
    protected $table = 'outbox_delivery_attempts';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'datetime',
        ];
    }

    public function outboxMessage(): BelongsTo
    {
        return $this->belongsTo(OutboxMessage::class, 'outbox_message_id');
    }
}
