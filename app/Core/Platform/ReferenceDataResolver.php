<?php

declare(strict_types=1);

namespace App\Core\Platform;

use App\Core\Tenancy\CurrentTenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class ReferenceDataResolver
{
    public function __construct(private readonly CurrentTenant $tenant)
    {
    }

    /** @return Collection<int, ReferenceValue> */
    public function values(string $groupCode, ?int $tenantId = null): Collection
    {
        $tenantId ??= $this->tenant->id();

        return ReferenceValue::query()
            ->whereHas('group', fn ($query) => $query->where('code', $groupCode))
            ->where(fn ($query) => $query->whereNull('tenant_id')->orWhere('tenant_id', $tenantId))
            ->where('status', 'active')
            ->orderBy('display_order')
            ->get();
    }

    public function value(string $groupCode, string $code, ?int $tenantId = null): ?ReferenceValue
    {
        return $this->values($groupCode, $tenantId)->firstWhere('code', $code);
    }

    public function resolveCode(string $groupCode, string $code, ?int $tenantId = null): ?string
    {
        return $this->value($groupCode, $code, $tenantId)?->label;
    }

    public function normalizeCode(string $value): string
    {
        return Str::slug($value, '_');
    }
}
