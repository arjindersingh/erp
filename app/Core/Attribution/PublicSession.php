<?php

declare(strict_types=1);

namespace App\Core\Attribution;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PublicSession extends Model
{
    protected $table = 'public_sessions';

    protected $guarded = ['id'];

    public $timestamps = true;

    public static function createForRequest(string $tenantId, string $publicAccessIdentityId, string $requestId): self
    {
        return static::query()->create([
            'uuid' => (string) Str::uuid(),
            'tenant_id' => $tenantId,
            'session_identifier_hash' => hash('sha256', $requestId),
            'first_request_id' => $requestId,
            'last_request_id' => $requestId,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
            'verification_state' => 'pending',
            'public_access_identity_id' => $publicAccessIdentityId,
            'expires_at' => now()->addHours(12),
        ]);
    }

    public function publicAccessIdentity(): BelongsTo
    {
        return $this->belongsTo(PublicAccessIdentity::class);
    }
}
