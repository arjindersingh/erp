<?php

declare(strict_types=1);

namespace App\Shared\Support;

use App\Core\Tenancy\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForTenant(Builder $query, Tenant|int $tenant): Builder
    {
        return $query->where(
            $this->qualifyColumn('tenant_id'),
            $tenant instanceof Tenant ? $tenant->getKey() : $tenant,
        );
    }
}
