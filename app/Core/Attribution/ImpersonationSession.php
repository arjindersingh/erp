<?php

declare(strict_types=1);

namespace App\Core\Attribution;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ImpersonationSession extends Model
{
    protected $table = 'impersonation_sessions';

    protected $guarded = ['id'];

    public $timestamps = true;

    public static function start(int $tenantId, int $impersonatorUserId, int $impersonatedUserId, string $reason = 'support'): self
    {
        return static::query()->create([
            'uuid' => (string) Str::uuid(),
            'tenant_id' => $tenantId,
            'impersonator_user_id' => $impersonatorUserId,
            'impersonated_user_id' => $impersonatedUserId,
            'reason' => $reason,
            'approved_scope_json' => ['scope' => 'support'],
            'started_at' => now(),
            'status' => 'active',
        ]);
    }
}
