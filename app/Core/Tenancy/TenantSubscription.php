<?php

declare(strict_types=1);

namespace App\Core\Tenancy;

use App\Shared\Support\BelongsToTenant;
use Database\Factories\TenantSubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[UseFactory(TenantSubscriptionFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'plan_code', 'status', 'starts_at', 'trial_ends_at', 'renews_at', 'ends_at', 'limits', 'features', 'external_reference', 'created_by', 'updated_by'])]
class TenantSubscription extends Model
{
    /** @use HasFactory<TenantSubscriptionFactory> */
    use BelongsToTenant, HasFactory;

    protected $attributes = ['status' => SubscriptionStatus::Trial->value];

    protected static function booted(): void
    {
        static::creating(function (TenantSubscription $subscription): void {
            $subscription->uuid ??= (string) str()->uuid();
        });
    }

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'starts_at' => 'immutable_datetime',
            'trial_ends_at' => 'immutable_datetime',
            'renews_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
            'limits' => 'array',
            'features' => 'array',
        ];
    }

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isEffectiveAt(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->status->allowsService()
            && ($this->starts_at === null || $this->starts_at->lessThanOrEqualTo($at))
            && ($this->ends_at === null || $this->ends_at->isAfter($at));
    }
}
