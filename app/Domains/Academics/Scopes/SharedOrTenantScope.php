<?php

declare(strict_types=1);

namespace App\Domains\Academics\Scopes;

use App\Core\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class SharedOrTenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = app(TenantContext::class)->id();
        if ($tenantId !== null) {
            $builder->where(fn (Builder $query) => $query
                ->whereNull($model->qualifyColumn('tenant_id'))
                ->orWhere($model->qualifyColumn('tenant_id'), $tenantId));
        }
    }
}
