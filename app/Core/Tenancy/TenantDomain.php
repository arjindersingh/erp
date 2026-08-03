<?php

namespace App\Core\Tenancy;

use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\TenantDomainFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(TenantDomainFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'domain', 'domain_type', 'status', 'is_primary', 'is_verified', 'verified_at'])]
class TenantDomain extends Model
{
    /** @use HasFactory<TenantDomainFactory> */
    use BelongsToTenant, HasFactory, HasPublicUuid;

    protected $attributes = [
        'domain_type' => DomainType::Custom->value,
        'status' => DomainStatus::Pending->value,
        'is_primary' => false,
        'is_verified' => false,
    ];

    protected static function booted(): void
    {
        static::creating(function (TenantDomain $domain): void {
            if ($domain->is_verified && $domain->status === DomainStatus::Pending) {
                $domain->status = DomainStatus::Active;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'domain_type' => DomainType::class,
            'status' => DomainStatus::class,
            'is_primary' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isResolvable(): bool
    {
        return $this->status === DomainStatus::Active && $this->is_verified;
    }
}
