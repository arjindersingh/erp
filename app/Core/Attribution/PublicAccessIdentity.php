<?php

declare(strict_types=1);

namespace App\Core\Attribution;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PublicAccessIdentity extends Model
{
    protected $table = 'public_access_identities';

    protected $guarded = ['id'];

    public $timestamps = true;

    public static function createForEmail(string $tenantId, string $email, string $verificationMethod = 'magic_link'): self
    {
        return static::query()->create([
            'uuid' => (string) Str::uuid(),
            'tenant_id' => $tenantId,
            'identity_type' => 'email',
            'identifier_hash' => hash('sha256', mb_strtolower(trim($email))),
            'masked_identifier' => substr($email, 0, 2).'***@'.explode('@', $email, 2)[1] ?? $email,
            'verification_method' => $verificationMethod,
            'verification_status' => 'verified',
            'verified_at' => now(),
            'expires_at' => now()->addHours(24),
        ]);
    }
}
